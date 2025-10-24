<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Get current semester courses
        $currentSemester = $student->semester;
        $currentYear = $student->academic_year;
        
        // Available courses for enrollment
        $availableCourses = Course::where('is_active', true)
            ->where('semester', $currentSemester)
            ->whereHas('department', function($query) use ($student) {
                $query->where('id', $student->department_id);
            })
            ->with(['department', 'teacher.user'])
            ->get();

        // Currently enrolled courses
        $enrolledCourses = $student->enrollments()
            ->with(['course.department', 'course.teacher.user'])
            ->where('status', 'enrolled')
            ->get();

        // Completed courses
        $completedCourses = $student->enrollments()
            ->with(['course.department', 'course.teacher.user'])
            ->where('status', 'completed')
            ->get();

        // Calculate total credits
        $totalCredits = $enrolledCourses->sum('course.credits');

        return view('student.courses.index', compact(
            'availableCourses',
            'enrolledCourses', 
            'completedCourses',
            'student',
            'totalCredits'
        ));
    }

    public function show(Course $course)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Check if student is enrolled in this course
        $enrollment = $student->enrollments()
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            abort(403, 'You are not enrolled in this course');
        }

        // Load course with relationships
        $course->load(['department', 'teacher.user', 'exams']);

        // Get course materials (if any)
        $materials = []; // Placeholder for course materials

        // Get assignments (if any)
        $assignments = []; // Placeholder for assignments

        // Get course schedule (if any)
        $schedule = []; // Placeholder for schedule

        return view('student.courses.show', compact(
            'course',
            'enrollment',
            'materials',
            'assignments',
            'schedule'
        ));
    }

    public function enroll(Request $request, Course $course)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Check if student can enroll
        if (!$this->canEnroll($student, $course)) {
            return back()->with('error', 'Cannot enroll in this course. Please check prerequisites and enrollment period.');
        }

        // Check for duplicate enrollment
        $existingEnrollment = $student->enrollments()
            ->where('course_id', $course->id)
            ->where('status', 'enrolled')
            ->first();

        if ($existingEnrollment) {
            return back()->with('error', 'You are already enrolled in this course.');
        }

        // Check credit limit
        $currentCredits = $student->enrollments()
            ->where('status', 'enrolled')
            ->with('course')
            ->get()
            ->sum('course.credits');

        if (($currentCredits + $course->credits) > 21) { // Max 21 credits per semester
            return back()->with('error', 'Credit limit exceeded. Maximum 21 credits per semester.');
        }

        DB::transaction(function () use ($student, $course) {
            Enrollment::create([
                'student_id' => $student->id,
                'course_id' => $course->id,
                'enrollment_date' => now(),
                'status' => 'enrolled',
            ]);
        });

        return back()->with('success', 'Successfully enrolled in ' . $course->title);
    }

    public function drop(Course $course)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        $enrollment = $student->enrollments()
            ->where('course_id', $course->id)
            ->where('status', 'enrolled')
            ->first();

        if (!$enrollment) {
            return back()->with('error', 'You are not enrolled in this course.');
        }

        // Check if drop period is still valid (within 2 weeks of enrollment)
        if ($enrollment->enrollment_date->addWeeks(2)->isPast()) {
            return back()->with('error', 'Drop period has expired. Contact administration.');
        }

        DB::transaction(function () use ($enrollment) {
            $enrollment->update(['status' => 'dropped']);
        });

        return back()->with('success', 'Successfully dropped ' . $course->title);
    }

    private function canEnroll($student, $course)
    {
        // Check if course is active
        if (!$course->is_active) {
            return false;
        }

        // Check if course belongs to student's department
        if ($course->department_id !== $student->department_id) {
            return false;
        }

        // Check if course is for current semester
        if ($course->semester !== $student->semester) {
            return false;
        }

        // Check enrollment period (within first 2 weeks of semester)
        // This is a placeholder - implement actual semester start date logic
        return true;
    }

    public function schedule()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Get enrolled courses
        $enrolledCourses = $student->enrollments()
            ->with(['course.department', 'course.teacher.user'])
            ->where('status', 'enrolled')
            ->get();

        // Generate sample schedule data
        $timeSlots = [
            '08:00 - 09:30',
            '09:45 - 11:15',
            '11:30 - 13:00',
            '14:00 - 15:30',
            '15:45 - 17:15',
            '17:30 - 19:00'
        ];

        $schedule = [];
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

        // Sample schedule data - in a real application, this would come from a database
        foreach ($enrolledCourses as $index => $enrollment) {
            $day = $days[$index % count($days)];
            $timeSlot = $timeSlots[$index % count($timeSlots)];
            
            $schedule[$day][$timeSlot] = [
                'course' => $enrollment->course->title,
                'room' => 'Room ' . ($index + 101),
                'teacher' => $enrollment->course->teacher->user->name ?? 'TBA'
            ];
        }

        return view('student.courses.schedule', compact('schedule', 'timeSlots', 'enrolledCourses'));
    }
}