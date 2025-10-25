<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hall;
use App\Models\Student;
use Illuminate\Http\Request;

class HallController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $halls = Hall::with('students')->latest()->paginate(15);
        
        return view('admin.halls.index', compact('halls'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.halls.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Custom validation for name uniqueness
        $existingHall = Hall::where('name', $request->name)
            ->whereNull('deleted_at')
            ->first();
            
        if ($existingHall) {
            return redirect()->back()
                ->withErrors(['name' => 'The name has already been taken.'])
                ->withInput();
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'description' => 'nullable|string|max:1000',
            'location' => 'nullable|string|max:255',
            'facilities' => 'nullable|array',
            'facilities.*' => 'string|max:255',
            'is_available' => 'boolean',
            'is_active' => 'boolean',
        ]);

        Hall::create([
            'name' => $request->name,
            'capacity' => $request->capacity,
            'description' => $request->description,
            'location' => $request->location,
            'facilities' => $request->facilities ?: [],
            'is_available' => $request->boolean('is_available'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.halls.index')->with('success', 'Hall created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Hall $hall)
    {
        $hall->load('students.user');
        $availableStudents = Student::whereNull('hall_id')
            ->where('is_active', true)
            ->with('user')
            ->get();
            
        return view('admin.halls.show', compact('hall', 'availableStudents'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Hall $hall)
    {
        return view('admin.halls.edit', compact('hall'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Hall $hall)
    {
        // Log the request for debugging
        \Log::info('Hall update request received', [
            'hall_id' => $hall->id,
            'hall_name' => $hall->name,
            'request_data' => $request->all(),
            'request_method' => $request->method()
        ]);

        // Simple validation without uniqueness check for now
        $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'description' => 'nullable|string|max:1000',
            'location' => 'nullable|string|max:255',
            'facilities' => 'nullable|array',
            'facilities.*' => 'string|max:255',
            'is_available' => 'boolean',
            'is_active' => 'boolean',
        ]);

        \Log::info('Validation passed, updating hall', ['hall_id' => $hall->id]);

        // Update the hall
        $hall->name = $request->name;
        $hall->capacity = $request->capacity;
        $hall->description = $request->description;
        $hall->location = $request->location;
        $hall->facilities = $request->facilities ?: [];
        $hall->is_available = $request->boolean('is_available');
        $hall->is_active = $request->boolean('is_active');
        
        $hall->save();

        \Log::info('Hall updated successfully', ['hall_id' => $hall->id]);

        return redirect()->route('admin.halls.index')->with('success', 'Hall updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hall $hall)
    {
        // Check if hall has assigned students
        if ($hall->students()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete hall with assigned students. Please reassign students first.');
        }

        $hall->delete();
        return redirect()->route('admin.halls.index')->with('success', 'Hall deleted successfully.');
    }

    /**
     * Assign student to hall
     */
    public function assignStudent(Request $request, Hall $hall)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        $student = Student::findOrFail($request->student_id);

        // Check if student is already assigned to a hall
        if ($student->hall_id) {
            return redirect()->back()->with('error', 'Student is already assigned to a hall.');
        }

        // Check if hall has capacity
        if ($hall->assigned_students_count >= $hall->capacity) {
            return redirect()->back()->with('error', 'Hall has reached maximum capacity.');
        }

        $student->update(['hall_id' => $hall->id]);

        return redirect()->back()->with('success', 'Student assigned to hall successfully.');
    }

    /**
     * Remove student from hall
     */
    public function removeStudent(Student $student)
    {
        $hallName = $student->hall->name ?? 'Unknown';
        
        $student->update(['hall_id' => null]);

        return redirect()->back()->with('success', "Student removed from {$hallName} successfully.");
    }
}
