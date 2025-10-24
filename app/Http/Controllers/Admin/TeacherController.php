<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Department;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teachers = Teacher::with(['user', 'department'])
            ->latest()
            ->paginate(15);

        return view('admin.teachers.index', compact('teachers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = Department::where('is_active', true)->get();
        return view('admin.teachers.create', compact('departments'));
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
            'employee_id' => 'required|string|max:20|unique:teachers,employee_id',
            'department_id' => 'required|exists:departments,id',
            'designation' => 'required|string|max:100',
            'date_of_joining' => 'required|date',
            'qualifications' => 'nullable|string',
        ]);

        $plainPassword = $request->password; // Store plain password for display
        
        // Create user account
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'teacher',
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'address' => $request->address,
            'is_active' => true,
        ]);

        // Create teacher profile
        $teacher = Teacher::create([
            'user_id' => $user->id,
            'department_id' => $request->department_id,
            'employee_id' => $request->employee_id,
            'designation' => $request->designation,
            'joining_date' => $request->date_of_joining,
            'qualifications' => $request->qualifications,
            'is_active' => true,
        ]);

        return redirect()->route('admin.teachers.credentials', $teacher)
            ->with('credentials', [
                'email' => $request->email,
                'password' => $plainPassword,
                'employee_id' => $request->employee_id,
                'name' => $request->name
            ]);
    }

    /**
     * Display teacher credentials after creation.
     */
    public function credentials(Teacher $teacher)
    {
        $teacher->load('user', 'department');
        return view('admin.teachers.credentials', compact('teacher'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Teacher $teacher)
    {
        $teacher->load(['user', 'department', 'courses']);
        
        // Get teacher's statistics
        $stats = [
            'total_courses' => $teacher->courses->count(),
            'active_courses' => $teacher->courses->where('is_active', true)->count(),
            'total_students' => $teacher->courses->sum(function($course) {
                return $course->enrollments()->where('status', 'enrolled')->count();
            }),
        ];

        // Get recent courses
        $recentCourses = $teacher->courses()
            ->with('department')
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.teachers.show', compact('teacher', 'stats', 'recentCourses'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Teacher $teacher)
    {
        $departments = Department::where('is_active', true)->get();
        return view('admin.teachers.edit', compact('teacher', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Teacher $teacher)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($teacher->user_id)],
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'employee_id' => ['required', 'string', 'max:20', Rule::unique('teachers')->ignore($teacher->id)],
            'department_id' => 'required|exists:departments,id',
            'designation' => 'required|string|max:100',
            'date_of_joining' => 'required|date',
            'qualifications' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Update user account
        $teacher->user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'address' => $request->address,
            'is_active' => $request->has('is_active'),
        ]);

        // Update teacher profile
        $teacher->update([
            'employee_id' => $request->employee_id,
            'department_id' => $request->department_id,
            'designation' => $request->designation,
            'joining_date' => $request->date_of_joining,
            'qualifications' => $request->qualifications,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.teachers.index')->with('success', 'Teacher updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Teacher $teacher)
    {
        // Soft delete the teacher and associated user
        $teacher->delete();
        $teacher->user->delete();

        return redirect()->route('admin.teachers.index')->with('success', 'Teacher deleted successfully.');
    }

    /**
     * Toggle the active status of the specified teacher.
     */
    public function toggleStatus(Teacher $teacher)
    {
        $teacher->is_active = !$teacher->is_active;
        $teacher->save();

        $teacher->user->is_active = $teacher->is_active;
        $teacher->user->save();

        return back()->with('success', 'Teacher status updated successfully.');
    }
}