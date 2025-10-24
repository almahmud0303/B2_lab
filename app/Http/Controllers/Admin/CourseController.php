<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Department;
use App\Models\Teacher;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Course::with(['department', 'teacher.user'])
            ->latest()
            ->paginate(15);

        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = Department::where('is_active', true)->get();
        $teachers = Teacher::where('is_active', true)->with('user')->get();
        return view('admin.courses.create', compact('departments', 'teachers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'course_code' => 'required|string|max:10|unique:courses,course_code',
            'credits' => 'required|integer|min:1|max:6',
            'semester' => 'required|integer|min:1|max:8',
            'description' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'fee_amount' => 'required|numeric|min:0',
            'currency' => 'required|string|in:BDT,USD,EUR',
            'fee_required' => 'boolean',
        ]);

        Course::create([
            'title' => $request->title,
            'course_code' => $request->course_code,
            'credits' => $request->credits,
            'semester' => $request->semester,
            'description' => $request->description,
            'department_id' => $request->department_id,
            'teacher_id' => $request->teacher_id,
            'fee_amount' => $request->fee_amount,
            'currency' => $request->currency,
            'fee_required' => $request->has('fee_required'),
            'is_active' => true,
        ]);

        return redirect()->route('admin.courses.index')->with('success', 'Course created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        $course->load(['department', 'teacher.user', 'enrollments.student.user', 'exams']);
        
        // Get course statistics
        $stats = [
            'total_enrollments' => $course->enrollments->count(),
            'active_enrollments' => $course->enrollments->where('status', 'enrolled')->count(),
            'total_exams' => $course->exams->count(),
            'average_grade' => $course->enrollments->avg(function($enrollment) {
                return $enrollment->results->avg('marks_obtained');
            }) ?? 0,
        ];

        // Get enrolled students
        $enrolledStudents = $course->enrollments()
            ->with('student.user')
            ->where('status', 'enrolled')
            ->get();

        // Get recent exams
        $recentExams = $course->exams()
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.courses.show', compact('course', 'stats', 'enrolledStudents', 'recentExams'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        $departments = Department::where('is_active', true)->get();
        $teachers = Teacher::where('is_active', true)->with('user')->get();
        return view('admin.courses.edit', compact('course', 'departments', 'teachers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'course_code' => ['required', 'string', 'max:10', Rule::unique('courses')->ignore($course->id)],
            'credits' => 'required|integer|min:1|max:6',
            'semester' => 'required|integer|min:1|max:8',
            'description' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'fee_amount' => 'required|numeric|min:0',
            'currency' => 'required|string|in:BDT,USD,EUR',
            'fee_required' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $course->update([
            'title' => $request->title,
            'course_code' => $request->course_code,
            'credits' => $request->credits,
            'semester' => $request->semester,
            'description' => $request->description,
            'department_id' => $request->department_id,
            'teacher_id' => $request->teacher_id,
            'fee_amount' => $request->fee_amount,
            'currency' => $request->currency,
            'fee_required' => $request->has('fee_required'),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.courses.index')->with('success', 'Course updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('admin.courses.index')->with('success', 'Course deleted successfully.');
    }

    /**
     * Toggle the active status of the specified course.
     */
    public function toggleStatus(Course $course)
    {
        $course->is_active = !$course->is_active;
        $course->save();

        return back()->with('success', 'Course status updated successfully.');
    }
}