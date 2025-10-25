<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $query = Staff::with('user', 'department');

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('employee_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by position
        if ($request->has('position') && $request->position) {
            $query->where('position', $request->position);
        }

        // Filter by location
        if ($request->has('location') && $request->location) {
            $query->where('location', $request->location);
        }

        // Filter by department
        if ($request->has('department_id') && $request->department_id) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $staff = $query->orderBy('employee_id')->paginate(20);
        $departments = Department::where('is_active', true)->get();

        return view('admin.staff.index', compact('staff', 'departments'));
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->get();
        return view('admin.staff.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'employee_id' => 'required|string|max:50|unique:staff',
            'position' => 'required|in:librarian,clerk,accountant,lab_assistant,office_assistant,other',
            'location' => 'required|in:library,administration,department',
            'department_id' => 'nullable|exists:departments,id',
            'qualification' => 'nullable|string|max:255',
            'salary' => 'nullable|numeric|min:0',
            'joining_date' => 'required|date',
            'employment_type' => 'required|in:full_time,part_time,contract',
            'responsibilities' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'staff',
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
                'is_active' => true,
            ]);

            $staff = Staff::create([
                'user_id' => $user->id,
                'department_id' => $request->department_id,
                'employee_id' => $request->employee_id,
                'position' => $request->position,
                'location' => $request->location,
                'qualification' => $request->qualification,
                'salary' => $request->salary,
                'joining_date' => $request->joining_date,
                'employment_type' => $request->employment_type,
                'responsibilities' => $request->responsibilities,
                'is_active' => true,
            ]);

            DB::commit();
            // Store credentials in session for the credentials page
            session()->flash('credentials.email', $request->email);
            session()->flash('credentials.password', $request->password);
            
            // Redirect to credentials page
            return redirect()->route('admin.staff.credentials', $staff->id)->with('success', 'Staff member created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error creating staff: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $staff = Staff::with('user', 'department', 'bookIssues.book.student.user')->findOrFail($id);
        return view('admin.staff.show', compact('staff'));
    }

    public function edit($id)
    {
        $staff = Staff::with('user')->findOrFail($id);
        $departments = Department::where('is_active', true)->get();
        return view('admin.staff.edit', compact('staff', 'departments'));
    }

    public function update(Request $request, $id)
    {
        $staff = Staff::with('user')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $staff->user_id,
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'employee_id' => 'required|string|max:50|unique:staff,employee_id,' . $staff->id,
            'position' => 'required|in:librarian,clerk,accountant,lab_assistant,office_assistant,other',
            'location' => 'required|in:library,administration,department',
            'department_id' => 'nullable|exists:departments,id',
            'qualification' => 'nullable|string|max:255',
            'salary' => 'nullable|numeric|min:0',
            'joining_date' => 'required|date',
            'employment_type' => 'required|in:full_time,part_time,contract',
            'responsibilities' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $staff->user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
                'is_active' => $request->has('is_active'),
            ]);

            $staff->update([
                'department_id' => $request->department_id,
                'employee_id' => $request->employee_id,
                'position' => $request->position,
                'location' => $request->location,
                'qualification' => $request->qualification,
                'salary' => $request->salary,
                'joining_date' => $request->joining_date,
                'employment_type' => $request->employment_type,
                'responsibilities' => $request->responsibilities,
                'is_active' => $request->has('is_active'),
            ]);

            DB::commit();
            return redirect()->route('admin.staff.index')->with('success', 'Staff member updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error updating staff: ' . $e->getMessage())->withInput();
        }
    }

    public function credentials($id)
    {
        $staff = Staff::with('user', 'department')->findOrFail($id);
        return view('admin.staff.credentials', compact('staff'));
    }

    public function destroy($id)
    {
        $staff = Staff::findOrFail($id);

        // Check if staff has active book issues
        if ($staff->bookIssues()->whereIn('status', ['issued', 'overdue'])->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete staff member with active book issues.');
        }

        DB::beginTransaction();
        try {
            $user = $staff->user;
            $staff->delete();
            $user->delete();

            DB::commit();
            return redirect()->route('admin.staff.index')->with('success', 'Staff member deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error deleting staff: ' . $e->getMessage());
        }
    }
}
