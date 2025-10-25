<?php

namespace App\Http\Controllers\DepartmentHead;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function index()
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher || !$teacher->is_department_head) {
            abort(403, 'You are not authorized as a department head.');
        }

        $department = $teacher->department;
        
        if (!$department) {
            abort(404, 'Department not found for this department head.');
        }

        // Get students from the department
        $students = Student::with('user')
            ->where('department_id', $department->id)
            ->where('is_active', true)
            ->orderBy('academic_year', 'desc')
            ->orderBy('semester', 'desc')
            ->orderBy('student_id')
            ->paginate(20);

        // Get statistics
        $stats = [
            'total_students' => Student::where('department_id', $department->id)->where('is_active', true)->count(),
            'first_year' => Student::where('department_id', $department->id)->where('academic_year', 1)->where('is_active', true)->count(),
            'second_year' => Student::where('department_id', $department->id)->where('academic_year', 2)->where('is_active', true)->count(),
            'third_year' => Student::where('department_id', $department->id)->where('academic_year', 3)->where('is_active', true)->count(),
            'fourth_year' => Student::where('department_id', $department->id)->where('academic_year', 4)->where('is_active', true)->count(),
        ];

        return view('department-head.students.index', compact('students', 'stats', 'teacher', 'department'));
    }

    public function show(Student $student)
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher || !$teacher->is_department_head) {
            abort(403, 'You are not authorized as a department head.');
        }

        $department = $teacher->department;
        
        if (!$department) {
            abort(404, 'Department not found for this department head.');
        }

        // Ensure the student belongs to this department
        if ($student->department_id !== $department->id) {
            abort(403, 'You can only view students from your department.');
        }

        // Load student with relationships
        $student->load(['user', 'department', 'hall']);

        // Get student's academic records grouped by year and semester
        $enrollments = Enrollment::with(['course.teacher.user', 'results'])
            ->where('student_id', $student->id)
            ->get()
            ->groupBy(function($enrollment) {
                return "Year {$enrollment->course->academic_year}, Semester {$enrollment->course->semester}";
            });

        // Calculate overall statistics
        $totalCredits = $enrollments->flatten()->sum('course.credits');
        $completedCredits = $enrollments->flatten()->where('status', 'completed')->sum('course.credits');
        
        // Calculate CGPA
        $totalPoints = 0;
        $totalCreditsForGPA = 0;
        foreach($enrollments->flatten() as $enrollment) {
            if($enrollment->status === 'completed' && $enrollment->results->count() > 0) {
                $latestResult = $enrollment->results->sortByDesc('created_at')->first();
                $totalPoints += $latestResult->grade_points * $enrollment->course->credits;
                $totalCreditsForGPA += $enrollment->course->credits;
            }
        }
        $cgpa = $totalCreditsForGPA > 0 ? $totalPoints / $totalCreditsForGPA : 0;

        return view('department-head.students.show', compact('student', 'enrollments', 'totalCredits', 'completedCredits', 'cgpa', 'teacher', 'department'));
    }

    public function academicRecord(Student $student)
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher || !$teacher->is_department_head) {
            abort(403, 'You are not authorized as a department head.');
        }

        $department = $teacher->department;
        
        if (!$department) {
            abort(404, 'Department not found for this department head.');
        }

        // Ensure the student belongs to this department
        if ($student->department_id !== $department->id) {
            abort(403, 'You can only view students from your department.');
        }

        // Load student with relationships
        $student->load(['user', 'department']);

        // Get student's academic records grouped by year and semester
        $academicRecords = Enrollment::with(['course.teacher.user', 'results'])
            ->where('student_id', $student->id)
            ->get()
            ->groupBy(function($enrollment) {
                return "Year {$enrollment->course->academic_year}, Semester {$enrollment->course->semester}";
            });

        return view('department-head.students.academic-record', compact('student', 'academicRecords', 'teacher', 'department'));
    }

    public function search(Request $request)
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher || !$teacher->is_department_head) {
            abort(403, 'You are not authorized as a department head.');
        }

        $department = $teacher->department;
        
        if (!$department) {
            abort(404, 'Department not found for this department head.');
        }

        $query = $request->get('query');
        $year = $request->get('year');
        $semester = $request->get('semester');

        $students = Student::with('user')
            ->where('department_id', $department->id)
            ->where('is_active', true);

        if ($query) {
            $students->where(function($q) use ($query) {
                $q->where('student_id', 'like', "%{$query}%")
                  ->orWhereHas('user', function($userQuery) use ($query) {
                      $userQuery->where('name', 'like', "%{$query}%")
                               ->orWhere('email', 'like', "%{$query}%");
                  });
            });
        }

        if ($year) {
            $students->where('academic_year', $year);
        }

        if ($semester) {
            $students->where('semester', $semester);
        }

        $students = $students->orderBy('academic_year', 'desc')
            ->orderBy('semester', 'desc')
            ->orderBy('student_id')
            ->paginate(20);

        $stats = [
            'total_students' => Student::where('department_id', $department->id)->where('is_active', true)->count(),
            'first_year' => Student::where('department_id', $department->id)->where('academic_year', 1)->where('is_active', true)->count(),
            'second_year' => Student::where('department_id', $department->id)->where('academic_year', 2)->where('is_active', true)->count(),
            'third_year' => Student::where('department_id', $department->id)->where('academic_year', 3)->where('is_active', true)->count(),
            'fourth_year' => Student::where('department_id', $department->id)->where('academic_year', 4)->where('is_active', true)->count(),
        ];

        return view('department-head.students.index', compact('students', 'stats', 'teacher', 'department', 'query', 'year', 'semester'));
    }
}
