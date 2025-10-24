<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index()
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            abort(404, 'Teacher profile not found');
        }

        $courses = Course::with('department', 'enrollments.student.user')
            ->where('teacher_id', $teacher->id)
            ->where('is_active', true)
            ->withCount(['enrollments' => function($query) {
                $query->where('status', 'enrolled');
            }])
            ->get();

        return view('teacher.courses.index', compact('courses', 'teacher'));
    }

    public function show($id)
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            abort(404, 'Teacher profile not found');
        }

        $course = Course::with([
            'department',
            'enrollments' => function($query) {
                $query->where('status', 'enrolled')->with('student.user');
            },
            'exams' => function($query) {
                $query->orderBy('exam_date');
            }
        ])
        ->where('teacher_id', $teacher->id)
        ->findOrFail($id);

        // Get students with their attendance records
        $students = $course->enrollments->map(function($enrollment) use ($course) {
            $student = $enrollment->student;
            $student->user->total_classes = Attendance::where('course_id', $course->id)
                ->where('student_id', $student->id)
                ->count();
            $student->user->present_classes = Attendance::where('course_id', $course->id)
                ->where('student_id', $student->id)
                ->where('status', 'present')
                ->count();
            $student->user->attendance_percentage = $student->user->total_classes > 0 
                ? round(($student->user->present_classes / $student->user->total_classes) * 100, 2)
                : 0;
            return $student;
        });

        return view('teacher.courses.show', compact('course', 'students', 'teacher'));
    }
}
