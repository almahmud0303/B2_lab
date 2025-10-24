<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use App\Models\Department;
use App\Models\Enrollment;
use App\Models\Result;
use App\Models\Fee;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Student::with(['user', 'department']);

        // Search by name, email, or student ID
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('student_id', 'like', "%{$search}%")
                  ->orWhere('admission_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by department
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by academic year
        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        // Filter by semester
        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by active status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $students = $query->latest()->paginate(15)->withQueryString();
        $departments = Department::where('is_active', true)->get();

        return view('admin.students.index', compact('students', 'departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = Department::where('is_active', true)->get();
        return view('admin.students.create', compact('departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'student_id' => 'required|string|max:20|unique:students,student_id',
            'admission_number' => 'required|string|max:20|unique:students,admission_number',
            'department_id' => 'required|exists:departments,id',
            'admission_date' => 'required|date',
            'academic_year' => 'required|in:1st,2nd,3rd,4th',
            'semester' => 'required|in:1st,2nd,3rd,4th,5th,6th,7th,8th',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'guardian_address' => 'nullable|string|max:500',
            'status' => 'nullable|in:active,inactive,graduated,suspended',
        ]);

        $plainPassword = $request->password; // Store plain password for display
        
        // Create user account
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'student',
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'address' => $request->address,
            'is_active' => true,
        ]);

        // Create student profile
        $student = Student::create([
            'user_id' => $user->id,
            'department_id' => $request->department_id,
            'student_id' => $request->student_id,
            'admission_number' => $request->admission_number,
            'admission_date' => $request->admission_date,
            'academic_year' => $request->academic_year,
            'semester' => $request->semester,
            'guardian_name' => $request->guardian_name,
            'guardian_phone' => $request->guardian_phone,
            'guardian_address' => $request->guardian_address,
            'status' => $request->status ?? 'active',
            'is_active' => true,
        ]);

        return redirect()->route('admin.students.credentials', $student)
            ->with('credentials', [
                'email' => $request->email,
                'password' => $plainPassword,
                'student_id' => $request->student_id,
                'name' => $request->name
            ]);
    }

    /**
     * Display student credentials after creation.
     */
    public function credentials(Student $student)
    {
        $student->load('user', 'department');
        return view('admin.students.credentials', compact('student'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        $student->load(['user', 'department', 'enrollments.course', 'results.exam']);
        
        // Get student's academic statistics
        $stats = [
            'total_courses' => $student->enrollments->count(),
            'completed_courses' => $student->enrollments->where('status', 'completed')->count(),
            'total_results' => $student->results->count(),
            'average_grade' => $student->results->avg('marks_obtained') ?? 0,
        ];

        // Get recent enrollments
        $recentEnrollments = $student->enrollments()
            ->with('course.department')
            ->latest()
            ->limit(5)
            ->get();

        // Get recent results
        $recentResults = $student->results()
            ->with('exam.course')
            ->latest()
            ->limit(5)
            ->get();

        // Get fee information
        $fees = $student->fees()->latest()->get();

        return view('admin.students.show', compact('student', 'stats', 'recentEnrollments', 'recentResults', 'fees'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        $departments = Department::where('is_active', true)->get();
        return view('admin.students.edit', compact('student', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($student->user_id)],
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'student_id' => ['required', 'string', 'max:20', Rule::unique('students')->ignore($student->id)],
            'admission_number' => ['required', 'string', 'max:20', Rule::unique('students')->ignore($student->id)],
            'department_id' => 'required|exists:departments,id',
            'admission_date' => 'required|date',
            'academic_year' => 'required|in:1st,2nd,3rd,4th',
            'semester' => 'required|in:1st,2nd,3rd,4th,5th,6th,7th,8th',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'guardian_address' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive,graduated,suspended',
            'is_active' => 'boolean',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Update user account
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'address' => $request->address,
            'is_active' => $request->has('is_active'),
        ];

        // Update password if provided
        if ($request->filled('password')) {
            $userData['password'] = bcrypt($request->password);
        }

        $student->user->update($userData);

        // Update student profile
        $student->update([
            'student_id' => $request->student_id,
            'admission_number' => $request->admission_number,
            'department_id' => $request->department_id,
            'admission_date' => $request->admission_date,
            'academic_year' => $request->academic_year,
            'semester' => $request->semester,
            'guardian_name' => $request->guardian_name,
            'guardian_phone' => $request->guardian_phone,
            'guardian_address' => $request->guardian_address,
            'status' => $request->status,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.students.index')->with('success', 'Student updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        // Soft delete the student and associated user
        $student->delete();
        $student->user->delete();

        return redirect()->route('admin.students.index')->with('success', 'Student deleted successfully.');
    }

    /**
     * Toggle the active status of the specified student.
     */
    public function toggleStatus(Student $student)
    {
        $student->is_active = !$student->is_active;
        $student->save();

        $student->user->is_active = $student->is_active;
        $student->user->save();

        return back()->with('success', 'Student status updated successfully.');
    }
}