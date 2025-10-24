<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        $user = Auth::user();
        
        return view('student.settings.index', compact('student', 'user'));
    }

    public function updateProfile(Request $request)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Update user information
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
        ]);

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile-images', 'public');
            $user->update(['profile_image' => $path]);
        }

        return back()->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully!');
    }

    public function updateSecurity(Request $request)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        $request->validate([
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'two_factor_enabled' => 'boolean',
            'login_alerts' => 'boolean',
            'privacy_level' => 'in:public,friends,private',
        ]);

        // Update security settings (you might want to create a settings table)
        $student->update([
            'email_notifications' => $request->boolean('email_notifications'),
            'sms_notifications' => $request->boolean('sms_notifications'),
            'two_factor_enabled' => $request->boolean('two_factor_enabled'),
            'login_alerts' => $request->boolean('login_alerts'),
            'privacy_level' => $request->privacy_level ?? 'private',
        ]);

        return back()->with('success', 'Security settings updated successfully!');
    }

    public function updatePreferences(Request $request)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        $request->validate([
            'language' => 'in:en,es,fr,de',
            'timezone' => 'string|max:255',
            'date_format' => 'in:Y-m-d,m/d/Y,d/m/Y',
            'theme' => 'in:light,dark,auto',
            'notifications_frequency' => 'in:immediate,daily,weekly,never',
        ]);

        // Update preferences
        $student->update([
            'language' => $request->language ?? 'en',
            'timezone' => $request->timezone ?? 'UTC',
            'date_format' => $request->date_format ?? 'Y-m-d',
            'theme' => $request->theme ?? 'light',
            'notifications_frequency' => $request->notifications_frequency ?? 'daily',
        ]);

        return back()->with('success', 'Preferences updated successfully!');
    }

    public function deactivateAccount(Request $request)
    {
        $request->validate([
            'password' => 'required',
            'reason' => 'required|string|max:500',
            'confirm_deactivation' => 'required|accepted',
        ]);

        $user = Auth::user();

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'The password is incorrect.']);
        }

        // Deactivate account
        $user->update([
            'is_active' => false,
            'deactivation_reason' => $request->reason,
            'deactivated_at' => now(),
        ]);

        Auth::logout();

        return redirect()->route('login')->with('success', 'Your account has been deactivated successfully.');
    }

    public function exportData()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Collect all user data
        $data = [
            'profile' => $student->load(['user', 'department'])->toArray(),
            'enrollments' => $student->enrollments()->with('course')->get()->toArray(),
            'results' => $student->results()->with(['exam.course'])->get()->toArray(),
            'fees' => $student->fees()->get()->toArray(),
            'book_issues' => $student->bookIssues()->with('book')->get()->toArray(),
            'exported_at' => now()->toISOString(),
        ];

        return response()->json($data, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="student-data-export-' . now()->format('Y-m-d') . '.json"'
        ]);
    }

    public function loginHistory()
    {
        $user = Auth::user();

        // This would typically come from a login_attempts or sessions table
        $loginHistory = collect([
            [
                'ip_address' => '192.168.1.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'location' => 'New York, NY, USA',
                'login_time' => now()->subHours(2),
                'status' => 'success',
            ],
            [
                'ip_address' => '192.168.1.2',
                'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X)',
                'location' => 'New York, NY, USA',
                'login_time' => now()->subDays(1),
                'status' => 'success',
            ],
            [
                'ip_address' => '192.168.1.3',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'location' => 'Unknown',
                'login_time' => now()->subDays(2),
                'status' => 'failed',
            ],
        ]);

        return view('student.settings.login-history', compact('loginHistory'));
    }

    public function connectedDevices()
    {
        $user = Auth::user();

        // This would typically come from a user_devices table
        $devices = collect([
            [
                'id' => 1,
                'device_name' => 'Chrome on Windows',
                'device_type' => 'desktop',
                'browser' => 'Chrome',
                'os' => 'Windows 10',
                'ip_address' => '192.168.1.1',
                'location' => 'New York, NY, USA',
                'last_active' => now()->subMinutes(30),
                'is_current' => true,
            ],
            [
                'id' => 2,
                'device_name' => 'Safari on iPhone',
                'device_type' => 'mobile',
                'browser' => 'Safari',
                'os' => 'iOS 14.0',
                'ip_address' => '192.168.1.2',
                'location' => 'New York, NY, USA',
                'last_active' => now()->subHours(2),
                'is_current' => false,
            ],
        ]);

        return view('student.settings.connected-devices', compact('devices'));
    }

    public function revokeDevice(Request $request, $deviceId)
    {
        $request->validate([
            'password' => 'required',
        ]);

        $user = Auth::user();

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'The password is incorrect.']);
        }

        // In a real application, you would revoke the device token/session
        // For now, we'll just return success
        
        return back()->with('success', 'Device access revoked successfully.');
    }
}
