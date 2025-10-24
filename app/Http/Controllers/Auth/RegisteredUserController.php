<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Department;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:student,teacher,staff'],
            'phone' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female,other'],
            'address' => ['nullable', 'string', 'max:500'],
            'student_id' => ['required_if:role,student', 'string', 'max:20', 'unique:students,student_id'],
            'employee_id' => ['required_if:role,teacher', 'string', 'max:20', 'unique:teachers,employee_id'],
            'department_id' => ['nullable', 'exists:departments,id'],
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
                'is_active' => true,
            ]);

            // Create role-specific records
            switch ($request->role) {
                case 'student':
                    Student::create([
                        'user_id' => $user->id,
                        'student_id' => $request->student_id,
                        'department_id' => $request->department_id,
                        'date_of_admission' => now(),
                        'current_semester' => 1,
                        'is_active' => true,
                    ]);
                    break;
                    
                case 'teacher':
                    Teacher::create([
                        'user_id' => $user->id,
                        'employee_id' => $request->employee_id,
                        'department_id' => $request->department_id,
                        'designation' => 'Assistant Professor',
                        'date_of_joining' => now(),
                        'is_active' => true,
                    ]);
                    break;
                    
                case 'staff':
                    // Staff members don't need additional model records
                    break;
            }

            event(new Registered($user));
            Auth::login($user);
        });

        return redirect(route('dashboard', absolute: false));
    }
}
