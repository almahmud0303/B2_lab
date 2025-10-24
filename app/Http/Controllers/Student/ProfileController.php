<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Load student with relationships
        $student->load(['user', 'department', 'hall']);

        // Get academic statistics
        $stats = [
            'total_enrollments' => $student->enrollments()->count(),
            'completed_courses' => $student->enrollments()->where('status', 'completed')->count(),
            'total_credits' => $student->enrollments()->with('course')->get()->sum('course.credits'),
        ];

        return view('student.profile.index', compact('student', 'stats'));
    }

    public function edit()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Load student with relationships
        $student->load(['user', 'department']);

        return view('student.profile.edit', compact('student'));
    }

    public function update(Request $request)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $student->user_id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'guardian_address' => 'nullable|string|max:500',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Update user information
        $student->user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
        ]);

        // Update student guardian information
        $student->update([
            'guardian_name' => $request->guardian_name,
            'guardian_phone' => $request->guardian_phone,
            'guardian_address' => $request->guardian_address,
        ]);

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            
            // Delete old image if exists
            if ($student->user->profile_image && file_exists(storage_path('app/public/' . $student->user->profile_image))) {
                unlink(storage_path('app/public/' . $student->user->profile_image));
            }
            
            // Store new image
            $imagePath = $image->store('profile_images', 'public');
            
            $student->user->update(['profile_image' => $imagePath]);
        }

        return redirect()->route('student.profile.index')->with('success', 'Profile updated successfully.');
    }

    public function changePassword()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        return view('student.profile.change-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('student.profile.index')->with('success', 'Password updated successfully.');
    }

    public function academic()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Load student with relationships
        $student->load(['user', 'department']);

        return view('student.profile.academic', compact('student'));
    }

    public function academicInfo()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Load student with relationships
        $student->load(['user', 'department']);

        // Get academic statistics
        $stats = [
            'total_enrollments' => $student->enrollments()->count(),
            'completed_courses' => $student->enrollments()->where('status', 'completed')->count(),
            'current_cgpa' => $this->calculateCGPA($student),
            'total_credits' => $student->enrollments()->with('course')->get()->sum('course.credits'),
        ];

        // Get enrollment history
        $enrollments = $student->enrollments()
            ->with(['course.department', 'course.teacher.user'])
            ->orderBy('enrollment_date', 'desc')
            ->get();

        return view('student.profile.academic-info', compact('student', 'stats', 'enrollments'));
    }

    public function contactInfo()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Load student with relationships
        $student->load(['user', 'department']);

        return view('student.profile.contact-info', compact('student'));
    }

    public function updateContactInfo(Request $request)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        $request->validate([
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'guardian_address' => 'nullable|string|max:500',
            'emergency_contact' => 'nullable|string|max:20',
        ]);

        // Update student contact information
        $student->update([
            'guardian_name' => $request->guardian_name,
            'guardian_phone' => $request->guardian_phone,
            'guardian_address' => $request->guardian_address,
        ]);

        // Update user contact information
        $student->user->update([
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        return redirect()->route('student.profile.contact-info')->with('success', 'Contact information updated successfully.');
    }

    public function settings()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        return view('student.profile.settings', compact('student'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'theme' => 'in:light,dark',
            'language' => 'in:en,bn',
        ]);

        $user = Auth::user();
        
        // Update user preferences (you might want to create a user_preferences table)
        $user->update([
            'email_notifications' => $request->has('email_notifications'),
            'sms_notifications' => $request->has('sms_notifications'),
            'theme' => $request->theme ?? 'light',
            'language' => $request->language ?? 'en',
        ]);

        return redirect()->route('student.profile.settings')->with('success', 'Settings updated successfully.');
    }

    public function security()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Get recent login activity (placeholder)
        $recentLogins = [
            ['ip' => '192.168.1.1', 'browser' => 'Chrome', 'time' => now()->subHours(2)],
            ['ip' => '192.168.1.1', 'browser' => 'Firefox', 'time' => now()->subDays(1)],
        ];

        return view('student.profile.security', compact('student', 'recentLogins'));
    }

    public function downloadProfile()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Load student with relationships
        $student->load(['user', 'department']);

        $pdf = \PDF::loadView('student.profile.pdf', compact('student'));
        
        return $pdf->download('student-profile-' . $student->student_id . '.pdf');
    }

    private function calculateCGPA($student)
    {
        $results = $student->results()
            ->where('is_published', true)
            ->get();

        if ($results->isEmpty()) {
            return 0.0;
        }

        $totalCredits = 0;
        $totalPoints = 0;

        foreach ($results as $result) {
            $credits = $result->exam->course->credits ?? 3;
            $gradePoints = $this->getGradePoints($result->grade);
            
            $totalCredits += $credits;
            $totalPoints += $gradePoints * $credits;
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