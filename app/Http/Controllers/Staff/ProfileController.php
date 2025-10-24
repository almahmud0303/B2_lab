<?php

namespace App\Http\Controllers\Staff;

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
        $staff = Auth::user()->staff;
        
        if (!$staff) {
            abort(404, 'Staff profile not found');
        }

        $staff->load('user', 'department');

        return view('staff.profile.index', compact('staff'));
    }

    public function edit()
    {
        $staff = Auth::user()->staff;
        
        if (!$staff) {
            abort(404, 'Staff profile not found');
        }

        $departments = Department::where('is_active', true)->get();

        return view('staff.profile.edit', compact('staff', 'departments'));
    }

    public function update(Request $request)
    {
        $staff = Auth::user()->staff;
        
        if (!$staff) {
            abort(404, 'Staff profile not found');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $staff->user_id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'qualification' => 'nullable|string|max:255',
            'responsibilities' => 'nullable|string|max:1000',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Update user information
        $staff->user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
        ]);

        // Update staff specific information
        $staff->update([
            'qualification' => $request->qualification,
            'responsibilities' => $request->responsibilities,
        ]);

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            
            // Delete old image if exists
            if ($staff->user->profile_image && file_exists(storage_path('app/public/' . $staff->user->profile_image))) {
                unlink(storage_path('app/public/' . $staff->user->profile_image));
            }
            
            // Store new image
            $imagePath = $image->store('profile_images', 'public');
            
            $staff->user->update(['profile_image' => $imagePath]);
        }

        return redirect()->route('staff.profile.index')->with('success', 'Profile updated successfully.');
    }

    public function changePassword()
    {
        return view('staff.profile.change-password');
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

        return redirect()->route('staff.profile.index')->with('success', 'Password changed successfully.');
    }

    public function academic()
    {
        $staff = Auth::user()->staff;
        
        if (!$staff) {
            abort(404, 'Staff profile not found');
        }

        $staff->load('department');

        return view('staff.profile.academic', compact('staff'));
    }
}
