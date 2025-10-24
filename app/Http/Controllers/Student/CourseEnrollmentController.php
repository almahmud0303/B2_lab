<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseEnrollmentController extends Controller
{
    /**
     * Show student's enrolled courses (compulsory + optional)
     */
    public function index()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Get compulsory courses (auto-enrolled)
        $compulsoryCourses = $student->enrollments()
            ->with(['course.teacher.user', 'course.department'])
            ->whereHas('course', function($query) {
                $query->where('course_type', 'compulsory');
            })
            ->where('status', 'enrolled')
            ->get();

        // Get optional courses (student chose to enroll)
        $optionalCourses = $student->enrollments()
            ->with(['course.teacher.user', 'course.department'])
            ->whereHas('course', function($query) {
                $query->where('course_type', 'optional');
            })
            ->where('status', 'enrolled')
            ->get();

        // Get completed courses
        $completedCourses = $student->enrollments()
            ->with(['course.teacher.user', 'course.department'])
            ->where('status', 'completed')
            ->orderBy('updated_at', 'desc')
            ->get();

        // Calculate total credits
        $totalCredits = $student->enrollments()
            ->where('status', 'enrolled')
            ->with('course')
            ->get()
            ->sum(function($enrollment) {
                return $enrollment->course->credits;
            });

        return view('student.courses.index', compact('compulsoryCourses', 'optionalCourses', 'completedCourses', 'totalCredits', 'student'));
    }

    /**
     * Show available optional courses for enrollment
     */
    public function catalog()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Get optional courses for student's department and current year/semester
        $availableCourses = Course::where('department_id', $student->department_id)
            ->where('academic_year', $student->academic_year)
            ->where('semester', $student->semester)
            ->where('course_type', 'optional')
            ->where('is_active', true)
            ->with(['teacher.user', 'department'])
            ->get();

        // Get already enrolled course IDs
        $enrolledCourseIds = $student->enrollments()
            ->whereIn('status', ['enrolled', 'completed'])
            ->pluck('course_id')
            ->toArray();

        // Filter out already enrolled courses
        $availableCourses = $availableCourses->reject(function($course) use ($enrolledCourseIds) {
            return in_array($course->id, $enrolledCourseIds);
        });

        // Add enrollment info to each course
        foreach ($availableCourses as $course) {
            $course->current_enrollments = $course->enrollments()->where('status', 'enrolled')->count();
            $course->slots_remaining = $course->max_enrollments 
                ? ($course->max_enrollments - $course->current_enrollments) 
                : ($course->max_students - $course->current_enrollments);
        }

        return view('student.courses.catalog', compact('availableCourses', 'student'));
    }

    /**
     * Enroll in an optional course
     */
    public function enroll(Request $request, Course $course)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Validation checks
        
        // 1. Check if course is optional
        if ($course->course_type !== 'optional') {
            return back()->with('error', 'You can only enroll in optional courses. Compulsory courses are auto-enrolled.');
        }

        // 2. Check if already enrolled
        $alreadyEnrolled = Enrollment::where('student_id', $student->id)
            ->where('course_id', $course->id)
            ->whereIn('status', ['enrolled', 'completed'])
            ->exists();

        if ($alreadyEnrolled) {
            return back()->with('error', 'You are already enrolled in this course.');
        }

        // 3. Check if course is for student's department
        if ($course->department_id !== $student->department_id) {
            return back()->with('error', 'This course is not available for your department.');
        }

        // 4. Check if course is for student's current year/semester
        if ($course->academic_year !== $student->academic_year || $course->semester !== $student->semester) {
            return back()->with('error', 'This course is not available for your current academic year/semester.');
        }

        // 5. Check enrollment limit
        $currentEnrollments = $course->enrollments()->where('status', 'enrolled')->count();
        $limit = $course->max_enrollments ?? $course->max_students;
        
        if ($currentEnrollments >= $limit) {
            return back()->with('error', 'This course has reached its enrollment limit.');
        }

        // 6. Check prerequisites
        if ($course->prerequisites) {
            $prerequisiteCodes = array_map('trim', explode(',', $course->prerequisites));
            $completedCourses = $student->enrollments()
                ->where('status', 'completed')
                ->with('course')
                ->get()
                ->pluck('course.course_code')
                ->toArray();

            foreach ($prerequisiteCodes as $prereqCode) {
                if (!in_array($prereqCode, $completedCourses)) {
                    return back()->with('error', "You must complete {$prereqCode} before enrolling in this course.");
                }
            }
        }

        // All checks passed - enroll the student
        Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'enrollment_date' => now(),
            'status' => 'enrolled',
            'grade' => null,
        ]);

        return back()->with('success', "Successfully enrolled in {$course->title}!");
    }

    /**
     * Drop an optional course
     */
    public function drop(Request $request, Course $course)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Check if course is optional
        if ($course->course_type !== 'optional') {
            return back()->with('error', 'Cannot drop compulsory courses.');
        }

        // Find the enrollment
        $enrollment = Enrollment::where('student_id', $student->id)
            ->where('course_id', $course->id)
            ->where('status', 'enrolled')
            ->first();

        if (!$enrollment) {
            return back()->with('error', 'You are not enrolled in this course.');
        }

        // Update enrollment status to dropped
        $enrollment->update([
            'status' => 'dropped',
        ]);

        return back()->with('success', "Successfully dropped {$course->title}.");
    }
}
