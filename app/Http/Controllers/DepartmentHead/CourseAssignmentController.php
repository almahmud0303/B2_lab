<?php

namespace App\Http\Controllers\DepartmentHead;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Department;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CourseAssignmentController extends Controller
{
    /**
     * Check if user is department head
     */
    private function getDepartment()
    {
        $user = Auth::user();
        $department = Department::where('head_user_id', $user->id)->first();
        
        if (!$department) {
            abort(403, 'You are not authorized as a department head.');
        }
        
        return $department;
    }

    /**
     * Display a listing of courses with assignment options
     */
    public function index(Request $request)
    {
        $department = $this->getDepartment();

        $query = Course::with('teacher.user', 'department', 'enrollments')
            ->where('department_id', $department->id)
            ->withCount(['enrollments' => function($q) {
                $q->where('status', 'enrolled');
            }]);

        // Filter by academic year
        if ($request->has('academic_year') && $request->academic_year) {
            $query->where('academic_year', $request->academic_year);
        }

        // Filter by semester
        if ($request->has('semester') && $request->semester) {
            $query->where('semester', $request->semester);
        }

        // Filter by assignment status
        if ($request->has('status')) {
            if ($request->status === 'assigned') {
                $query->whereNotNull('teacher_id');
            } elseif ($request->status === 'unassigned') {
                $query->whereNull('teacher_id');
            }
        }

        $courses = $query->orderBy('academic_year')
            ->orderBy('semester')
            ->orderBy('course_code')
            ->paginate(20);

        $teachers = Teacher::with('user')
            ->where('department_id', $department->id)
            ->where('is_active', true)
            ->get();

        return view('department-head.course-assignment.index', compact('courses', 'teachers', 'department'));
    }

    /**
     * Show the form for assigning a teacher to a course
     */
    public function assign($courseId)
    {
        $department = $this->getDepartment();

        $course = Course::where('id', $courseId)
            ->where('department_id', $department->id)
            ->firstOrFail();

        $teachers = Teacher::with('user', 'courses')
            ->where('department_id', $department->id)
            ->where('is_active', true)
            ->get();

        // Calculate teacher workload
        foreach ($teachers as $teacher) {
            $teacher->current_courses_count = $teacher->courses()
                ->where('is_active', true)
                ->count();
            $teacher->total_students = $teacher->courses()
                ->where('is_active', true)
                ->withCount(['enrollments' => function($q) {
                    $q->where('status', 'enrolled');
                }])
                ->get()
                ->sum('enrollments_count');
        }

        return view('department-head.course-assignment.assign', compact('course', 'teachers', 'department'));
    }

    /**
     * Update the teacher assignment for a course
     */
    public function updateAssignment(Request $request, $courseId)
    {
        $department = $this->getDepartment();

        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
        ]);

        $course = Course::where('id', $courseId)
            ->where('department_id', $department->id)
            ->firstOrFail();

        // Verify teacher belongs to this department
        $teacher = Teacher::where('id', $request->teacher_id)
            ->where('department_id', $department->id)
            ->where('is_active', true)
            ->firstOrFail();

        $course->update([
            'teacher_id' => $teacher->id,
        ]);

        return redirect()
            ->route('department-head.course-assignment.index')
            ->with('success', "Course '{$course->title}' has been assigned to {$teacher->user->name}.");
    }

    /**
     * Remove teacher assignment from a course
     */
    public function unassign($courseId)
    {
        $department = $this->getDepartment();

        $course = Course::where('id', $courseId)
            ->where('department_id', $department->id)
            ->firstOrFail();

        $course->update([
            'teacher_id' => null,
        ]);

        return redirect()
            ->back()
            ->with('success', "Teacher has been unassigned from course '{$course->title}'.");
    }

    /**
     * Bulk assign teachers to multiple courses
     */
    public function bulkAssign(Request $request)
    {
        $department = $this->getDepartment();

        $request->validate([
            'assignments' => 'required|array',
            'assignments.*.course_id' => 'required|exists:courses,id',
            'assignments.*.teacher_id' => 'required|exists:teachers,id',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->assignments as $assignment) {
                $course = Course::where('id', $assignment['course_id'])
                    ->where('department_id', $department->id)
                    ->first();

                if ($course) {
                    $teacher = Teacher::where('id', $assignment['teacher_id'])
                        ->where('department_id', $department->id)
                        ->where('is_active', true)
                        ->first();

                    if ($teacher) {
                        $course->update(['teacher_id' => $teacher->id]);
                    }
                }
            }

            DB::commit();
            return redirect()
                ->route('department-head.course-assignment.index')
                ->with('success', 'Courses have been assigned successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Error assigning courses: ' . $e->getMessage());
        }
    }

    /**
     * Show teacher workload report
     */
    public function workloadReport()
    {
        $department = $this->getDepartment();

        $teachers = Teacher::with(['user', 'courses' => function($q) {
            $q->where('is_active', true)
              ->with(['enrollments' => function($subQ) {
                  $subQ->where('status', 'enrolled');
              }]);
        }])
        ->where('department_id', $department->id)
        ->where('is_active', true)
        ->get();

        foreach ($teachers as $teacher) {
            $teacher->total_courses = $teacher->courses->count();
            $teacher->total_students = $teacher->courses->sum(function($course) {
                return $course->enrollments->count();
            });
            $teacher->total_credits = $teacher->courses->sum('credits');
        }

        return view('department-head.course-assignment.workload', compact('teachers', 'department'));
    }
}
