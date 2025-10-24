<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\Result;
use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            abort(404, 'Teacher profile not found');
        }

        // Get teacher's courses
        $courses = Course::with('department')
            ->where('teacher_id', $teacher->id)
            ->where('is_active', true)
            ->get();

        // Get total students enrolled in teacher's courses
        $totalStudents = Enrollment::whereHas('course', function($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->where('status', 'enrolled')->count();

        // Get upcoming exams for teacher's courses
        $upcomingExams = Exam::with('course.department')
            ->whereHas('course', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->where('exam_date', '>=', now())
            ->where('status', 'scheduled')
            ->orderBy('exam_date')
            ->limit(5)
            ->get();

        // Get pending results to be published
        $pendingResults = Result::with('exam.course', 'student.user')
            ->whereHas('exam.course', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->where('is_published', false)
            ->latest()
            ->limit(10)
            ->get();

        // Get recent notices for teachers
        $recentNotices = Notice::where('is_published', true)
            ->where(function($query) {
                $query->whereNull('target_roles')
                      ->orWhereJsonContains('target_roles', 'teacher');
            })
            ->where(function($query) {
                $query->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>=', now());
            })
            ->latest()
            ->limit(5)
            ->get();

        // Get course statistics
        $courseStats = collect();
        foreach ($courses as $course) {
            $enrolledCount = Enrollment::where('course_id', $course->id)
                ->where('status', 'enrolled')
                ->count();
            
            $courseStats->push([
                'course' => $course,
                'enrolled_count' => $enrolledCount,
                'available_slots' => $course->max_students - $enrolledCount
            ]);
        }

        return view('teacher.dashboard', compact(
            'teacher',
            'courses',
            'totalStudents',
            'upcomingExams',
            'pendingResults',
            'recentNotices',
            'courseStats'
        ));
    }
}