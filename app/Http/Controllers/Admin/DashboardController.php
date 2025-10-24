<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Course;
use App\Models\Exam;
use App\Models\Fee;
use App\Models\Notice;
use App\Models\User;
use App\Models\Hall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStudents = Student::count();
        $totalTeachers = Teacher::count();
        $totalCourses = Course::count();
        $totalDepartments = Department::count();
        $totalHalls = Hall::count();
        $totalFeesCollected = Fee::where('status', 'paid')->sum('amount');
        
        $upcomingExams = Exam::with('course')
            ->where('exam_date', '>=', now())
            ->where('status', 'scheduled')
            ->orderBy('exam_date')
            ->limit(5)
            ->get();
            
        $recentNotices = Notice::where('is_published', true)
            ->where(function($query) {
                $query->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>=', now());
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalStudents',
            'totalTeachers',
            'totalCourses',
            'totalDepartments',
            'totalHalls',
            'totalFeesCollected',
            'upcomingExams',
            'recentNotices'
        ));
    }

    public function analytics()
    {
        // Get enrollment trends for the last 12 months
        $enrollment_trends = DB::table('enrollments')
            ->select(
                DB::raw('YEAR(enrollment_date) as year'),
                DB::raw('MONTH(enrollment_date) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('enrollment_date', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Get fee collection trends
        $fee_trends = DB::table('fees')
            ->select(
                DB::raw('YEAR(paid_date) as year'),
                DB::raw('MONTH(paid_date) as month'),
                DB::raw('SUM(paid_amount) as total')
            )
            ->where('paid_date', '>=', now()->subMonths(12))
            ->whereNotNull('paid_date')
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Get exam performance statistics
        $exam_stats = DB::table('results')
            ->select(
                'grade',
                DB::raw('COUNT(*) as count')
            )
            ->where('is_published', true)
            ->groupBy('grade')
            ->get();

        return view('admin.analytics', compact(
            'enrollment_trends',
            'fee_trends',
            'exam_stats'
        ));
    }
}