<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\Result;
use App\Models\Fee;
use App\Models\Notice;
use App\Models\BookIssue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Load student with hall relationship
        $student->load('hall');

        // Get student's enrollments with courses
        $enrollments = Enrollment::with('course.department', 'course.teacher.user')
            ->where('student_id', $student->id)
            ->where('status', 'enrolled')
            ->get();

        // Get upcoming exams for enrolled courses
        $upcomingExams = Exam::with('course')
            ->whereHas('course', function($query) use ($student) {
                $query->whereHas('enrollments', function($q) use ($student) {
                    $q->where('student_id', $student->id)
                      ->where('status', 'enrolled');
                });
            })
            ->where('exam_date', '>=', now())
            ->where('status', 'scheduled')
            ->orderBy('exam_date')
            ->limit(5)
            ->get();

        // Get recent results
        $recentResults = Result::with('exam.course')
            ->where('student_id', $student->id)
            ->where('is_published', true)
            ->latest()
            ->limit(5)
            ->get();

        // Get fee status
        $feeStats = [
            'total_pending' => Fee::where('student_id', $student->id)
                ->where('status', 'pending')
                ->sum('amount'),
            'total_paid' => Fee::where('student_id', $student->id)
                ->where('status', 'paid')
                ->sum('paid_amount'),
            'overdue_amount' => Fee::where('student_id', $student->id)
                ->where('status', 'overdue')
                ->sum('amount'),
        ];

        // Get active book issues
        $activeBookIssues = BookIssue::with('book')
            ->where('student_id', $student->id)
            ->where('status', 'issued')
            ->get();

        // Get recent notices for students
        $recentNotices = Notice::where('is_published', true)
            ->where(function($query) {
                $query->whereNull('target_roles')
                      ->orWhereJsonContains('target_roles', 'student');
            })
            ->where(function($query) {
                $query->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>=', now());
            })
            ->latest()
            ->limit(5)
            ->get();

        // Calculate GPA
        $gpa = $this->calculateGPA($student->id);

        return view('student.dashboard', compact(
            'student',
            'enrollments',
            'upcomingExams',
            'recentResults',
            'feeStats',
            'activeBookIssues',
            'recentNotices',
            'gpa'
        ));
    }

    private function calculateGPA($studentId)
    {
        $results = Result::with('exam.course')
            ->where('student_id', $studentId)
            ->where('is_published', true)
            ->whereNotNull('grade')
            ->get();

        if ($results->isEmpty()) {
            return 0.0;
        }

        $totalPoints = 0;
        $totalCredits = 0;

        foreach ($results as $result) {
            $credits = $result->exam->course->credits ?? 3;
            $gradePoints = $this->getGradePoints($result->grade);
            
            $totalPoints += $gradePoints * $credits;
            $totalCredits += $credits;
        }

        return $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0.0;
    }

    private function getGradePoints($grade)
    {
        $gradePoints = [
            'A+' => 4.0, 'A' => 3.75, 'A-' => 3.5,
            'B+' => 3.25, 'B' => 3.0, 'B-' => 2.75,
            'C+' => 2.5, 'C' => 2.25, 'C-' => 2.0,
            'D+' => 1.75, 'D' => 1.5, 'D-' => 1.25,
            'F' => 0.0
        ];

        return $gradePoints[$grade] ?? 0.0;
    }
}