<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Department;
use App\Models\Teacher;
use Illuminate\Http\Request;

class CourseManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::with(['department', 'teacher']);

        // Apply filters
        if ($request->has('department_id') && $request->department_id) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->has('academic_year') && $request->academic_year) {
            $query->where('academic_year', $request->academic_year);
        }

        if ($request->has('semester') && $request->semester) {
            $query->where('semester', $request->semester);
        }

        if ($request->has('course_type') && $request->course_type) {
            $query->where('course_type', $request->course_type);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('course_code', 'like', "%{$search}%");
            });
        }

        $courses = $query->orderBy('department_id')
                        ->orderBy('academic_year')
                        ->orderBy('semester')
                        ->paginate(20);

        $departments = Department::where('is_active', true)->get();
        $academicYears = ['1st', '2nd', '3rd', '4th'];
        $semesters = ['1st', '2nd', '3rd', '4th', '5th', '6th', '7th', '8th'];

        return view('admin.courses.index', compact('courses', 'departments', 'academicYears', 'semesters'));
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->get();
        $teachers = Teacher::with('user')->where('is_active', true)->get();
        $academicYears = ['1st', '2nd', '3rd', '4th'];
        $semesters = ['1st', '2nd', '3rd', '4th', '5th', '6th', '7th', '8th'];

        return view('admin.courses.create', compact('departments', 'teachers', 'academicYears', 'semesters'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'teacher_id' => 'required|exists:teachers,id',
            'course_code' => 'required|string|max:255|unique:courses,course_code',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'credits' => 'required|integer|min:1|max:6',
            'semester' => 'required|in:1st,2nd,3rd,4th,5th,6th,7th,8th',
            'academic_year' => 'required|in:1st,2nd,3rd,4th',
            'course_type' => 'required|in:compulsory,optional',
            'max_students' => 'required|integer|min:1',
            'max_enrollments' => 'nullable|integer|min:1',
            'prerequisites' => 'nullable|string',
        ]);

        $validated['is_active'] = true;

        Course::create($validated);

        return redirect()->route('admin.courses.index')
                        ->with('success', 'Course created successfully!');
    }

    public function edit(Course $course)
    {
        $departments = Department::where('is_active', true)->get();
        $teachers = Teacher::with('user')->where('is_active', true)->get();
        $academicYears = ['1st', '2nd', '3rd', '4th'];
        $semesters = ['1st', '2nd', '3rd', '4th', '5th', '6th', '7th', '8th'];

        return view('admin.courses.edit', compact('course', 'departments', 'teachers', 'academicYears', 'semesters'));
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'teacher_id' => 'required|exists:teachers,id',
            'course_code' => 'required|string|max:255|unique:courses,course_code,' . $course->id,
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'credits' => 'required|integer|min:1|max:6',
            'semester' => 'required|in:1st,2nd,3rd,4th,5th,6th,7th,8th',
            'academic_year' => 'required|in:1st,2nd,3rd,4th',
            'course_type' => 'required|in:compulsory,optional',
            'max_students' => 'required|integer|min:1',
            'max_enrollments' => 'nullable|integer|min:1',
            'prerequisites' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        $course->update($validated);

        return redirect()->route('admin.courses.index')
                        ->with('success', 'Course updated successfully!');
    }

    public function destroy(Course $course)
    {
        // Soft delete
        $course->delete();

        return redirect()->route('admin.courses.index')
                        ->with('success', 'Course deleted successfully!');
    }

    public function show(Course $course)
    {
        $course->load(['department', 'teacher.user', 'enrollments.student.user']);
        
        $enrollmentStats = [
            'total' => $course->enrollments()->count(),
            'enrolled' => $course->enrollments()->where('status', 'enrolled')->count(),
            'completed' => $course->enrollments()->where('status', 'completed')->count(),
            'dropped' => $course->enrollments()->where('status', 'dropped')->count(),
        ];

        return view('admin.courses.show', compact('course', 'enrollmentStats'));
    }

    public function bulkAssign(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'academic_year' => 'required|in:1st,2nd,3rd,4th',
            'semester' => 'required|in:1st,2nd,3rd,4th,5th,6th,7th,8th',
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id',
        ]);

        foreach ($validated['course_ids'] as $courseId) {
            $course = Course::find($courseId);
            $course->update([
                'department_id' => $validated['department_id'],
                'academic_year' => $validated['academic_year'],
                'semester' => $validated['semester'],
            ]);
        }

        return redirect()->back()->with('success', 'Courses assigned successfully!');
    }

    public function getByDepartment($departmentId)
    {
        $courses = Course::where('department_id', $departmentId)
                        ->where('is_active', true)
                        ->orderBy('academic_year')
                        ->orderBy('semester')
                        ->get(['id', 'course_code', 'title', 'academic_year', 'semester', 'course_type']);

        return response()->json($courses);
    }

    /**
     * Show courses organized by department, year, and semester
     */
    public function organize(Request $request)
    {
        $departments = Department::where('is_active', true)->get();
        $years = ['1st', '2nd', '3rd', '4th'];
        
        $query = Course::with(['department', 'teacher.user']);

        // Filter by department if selected
        if ($request->has('department_id') && $request->department_id) {
            $query->where('department_id', $request->department_id);
        }

        $allCourses = $query->where('is_active', true)
                          ->orderBy('academic_year')
                          ->orderBy('semester')
                          ->get();

        // Organize courses by year and semester
        $coursesByYearSemester = [];
        
        foreach ($years as $year) {
            $coursesByYearSemester[$year] = [];
            
            // 2 semesters per year
            $yearNum = (int)str_replace(['st', 'nd', 'rd', 'th'], '', $year);
            $sem1Index = (($yearNum - 1) * 2) + 1;
            $sem2Index = (($yearNum - 1) * 2) + 2;
            
            $semesters = [
                ['1st', '2nd', '3rd', '4th', '5th', '6th', '7th', '8th'][$sem1Index - 1] ?? null,
                ['1st', '2nd', '3rd', '4th', '5th', '6th', '7th', '8th'][$sem2Index - 1] ?? null,
            ];

            foreach ($semesters as $semester) {
                if ($semester) {
                    $coursesByYearSemester[$year][$semester] = $allCourses
                        ->where('academic_year', $year)
                        ->where('semester', $semester);
                }
            }
        }

        return view('admin.courses.organize', compact('departments', 'years', 'coursesByYearSemester'));
    }
}
