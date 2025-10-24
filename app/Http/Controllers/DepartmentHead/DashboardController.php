<?php

namespace App\Http\Controllers\DepartmentHead;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $teacher = $user->teacher;
        
        if (!$teacher || !$teacher->is_department_head) {
            abort(403, 'You are not authorized as a department head.');
        }

        $department = $teacher->department;
        
        if (!$department) {
            abort(404, 'Department not found for this department head.');
        }

        // Get department statistics
        $stats = [
            'total_teachers' => Teacher::where('department_id', $department->id)
                ->where('is_active', true)
                ->count(),
            'total_students' => Student::where('department_id', $department->id)
                ->where('is_active', true)
                ->count(),
            'total_courses' => Course::where('department_id', $department->id)
                ->where('is_active', true)
                ->count(),
            'active_courses' => Course::where('department_id', $department->id)
                ->where('is_active', true)
                ->whereHas('enrollments', function($q) {
                    $q->where('status', 'enrolled');
                })
                ->count(),
        ];

        // Get department teachers
        $teachers = Teacher::with('user')
            ->where('department_id', $department->id)
            ->where('is_active', true)
            ->get();

        // Get department courses
        $courses = Course::with('teacher.user', 'enrollments')
            ->where('department_id', $department->id)
            ->where('is_active', true)
            ->withCount(['enrollments' => function($q) {
                $q->where('status', 'enrolled');
            }])
            ->orderBy('academic_year')
            ->orderBy('semester')
            ->get();

        // Get recent students
        $recentStudents = Student::with('user')
            ->where('department_id', $department->id)
            ->where('is_active', true)
            ->latest()
            ->limit(10)
            ->get();

        return view('department-head.dashboard', compact(
            'department',
            'stats',
            'teachers',
            'courses',
            'recentStudents',
            'teacher'
        ));
    }
}
