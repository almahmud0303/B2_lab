<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $staff = Auth::user()->staff;
        
        if (!$staff) {
            abort(404, 'Staff profile not found');
        }

        $query = Student::with('user', 'department');

        // Search by name, email, or student ID
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('student_id', 'like', "%{$search}%")
                  ->orWhere('admission_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by department
        if ($request->has('department_id') && $request->department_id) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by academic year
        if ($request->has('academic_year') && $request->academic_year) {
            $query->where('academic_year', $request->academic_year);
        }

        // Filter by semester
        if ($request->has('semester') && $request->semester) {
            $query->where('semester', $request->semester);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $students = $query->orderBy('student_id')->paginate(20);
        $departments = Department::where('is_active', true)->get();

        return view('staff.students.index', compact('students', 'departments', 'staff'));
    }

    public function show($id)
    {
        $staff = Auth::user()->staff;
        
        if (!$staff) {
            abort(404, 'Staff profile not found');
        }

        // Staff can only view basic student info and book issues
        // Cannot view academic records (courses, results, grades)
        $student = Student::with([
            'user',
            'department',
            'bookIssues.book'
        ])->findOrFail($id);

        return view('staff.students.show', compact('student', 'staff'));
    }
}
