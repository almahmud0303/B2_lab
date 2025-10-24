<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ProfileController extends Controller
{
    public function index()
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            abort(404, 'Teacher profile not found');
        }

        $teacher->load('user', 'department', 'courses');

        return view('teacher.profile.index', compact('teacher'));
    }

    public function edit()
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            abort(404, 'Teacher profile not found');
        }

        $departments = Department::where('is_active', true)->get();

        return view('teacher.profile.edit', compact('teacher', 'departments'));
    }

    public function update(Request $request)
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            abort(404, 'Teacher profile not found');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $teacher->user_id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'qualification' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Update user information
        $teacher->user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
        ]);

        // Update teacher specific information
        $teacher->update([
            'qualification' => $request->qualification,
            'specialization' => $request->specialization,
            'bio' => $request->bio,
        ]);

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            
            // Delete old image if exists
            if ($teacher->user->profile_image && file_exists(storage_path('app/public/' . $teacher->user->profile_image))) {
                unlink(storage_path('app/public/' . $teacher->user->profile_image));
            }
            
            // Store new image
            $imagePath = $image->store('profile_images', 'public');
            
            $teacher->user->update(['profile_image' => $imagePath]);
        }

        return redirect()->route('teacher.profile.index')->with('success', 'Profile updated successfully.');
    }

    public function changePassword()
    {
        return view('teacher.profile.change-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('teacher.profile.index')->with('success', 'Password changed successfully.');
    }

    public function academic()
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            abort(404, 'Teacher profile not found');
        }

        $teacher->load('department', 'courses.enrollments.student.user');
        
        // Get department head if exists
        $departmentHead = null;
        if ($teacher->department->head_user_id) {
            $departmentHead = \App\Models\User::with('teacher')->find($teacher->department->head_user_id);
        }

        // Get all teachers in the department
        $departmentTeachers = \App\Models\Teacher::with('user')
            ->where('department_id', $teacher->department_id)
            ->where('is_active', true)
            ->get();

        return view('teacher.profile.academic', compact('teacher', 'departmentHead', 'departmentTeachers'));
    }
}
