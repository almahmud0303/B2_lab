<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Teacher;
use App\Models\Staff;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InfoController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Get statistics
        $stats = [
            'total_departments' => Department::count(),
            'total_teachers' => Teacher::count(),
            'total_staff' => Staff::count(),
            'total_courses' => Course::count(),
        ];

        // Get recent updates (placeholder data)
        $recentUpdates = collect([
            [
                'type' => 'teacher',
                'title' => 'New Faculty Member Added',
                'description' => 'Dr. John Smith joined the Computer Science department',
                'date' => now()->subDays(1),
            ],
            [
                'type' => 'course',
                'title' => 'New Course Available',
                'description' => 'Advanced Web Development course is now available',
                'date' => now()->subDays(3),
            ],
            [
                'type' => 'department',
                'title' => 'Department Update',
                'description' => 'Business Administration department updated their programs',
                'date' => now()->subDays(5),
            ],
        ]);

        return view('student.info.index', compact('stats', 'recentUpdates'));
    }

    public function departments()
    {
        $departments = Department::withCount(['teachers', 'courses'])
            ->orderBy('name')
            ->paginate(15);

        return view('student.info.departments', compact('departments'));
    }

    public function departmentShow(Department $department)
    {
        $department->load(['teachers.user', 'courses.teacher.user']);
        
        // Get department statistics
        $stats = [
            'total_teachers' => $department->teachers->count(),
            'total_courses' => $department->courses->count(),
            'total_students' => $department->students->count(),
        ];

        // Get recent courses
        $recentCourses = $department->courses()
            ->with(['teacher.user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('student.info.department-show', compact('department', 'stats', 'recentCourses'));
    }

    public function teachers()
    {
        $teachers = Teacher::with(['user', 'department'])
            ->orderBy('user_id')
            ->paginate(15);

        return view('student.info.teachers', compact('teachers'));
    }

    public function teacherShow(Teacher $teacher)
    {
        $teacher->load(['user', 'department', 'courses.department']);
        
        // Get teacher statistics
        $stats = [
            'total_courses' => $teacher->courses->count(),
            'total_students' => $teacher->courses()->withCount('enrollments')->get()->sum('enrollments_count'),
            'experience_years' => $teacher->experience_years ?? 0,
        ];

        // Get teacher's courses
        $courses = $teacher->courses()
            ->with(['department'])
            ->orderBy('title')
            ->get();

        // Get recent courses
        $recentCourses = $teacher->courses()
            ->with(['department'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('student.info.teacher-show', compact('teacher', 'stats', 'courses', 'recentCourses'));
    }

    public function staff()
    {
        $staff = Staff::with(['user', 'department'])
            ->orderBy('user_id')
            ->paginate(15);

        return view('student.info.staff', compact('staff'));
    }

    public function staffShow(Staff $staff)
    {
        $staff->load(['user', 'department']);
        
        // Get staff statistics
        $stats = [
            'experience_years' => $staff->experience_years ?? 0,
            'department' => $staff->department->name ?? 'N/A',
        ];

        return view('student.info.staff-show', compact('staff', 'stats'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $type = $request->get('type', 'all');

        $results = collect();

        if ($type === 'all' || $type === 'departments') {
            $departments = Department::where('name', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->limit(5)
                ->get()
                ->map(function ($department) {
                    return [
                        'type' => 'department',
                        'title' => $department->name,
                        'description' => $department->description,
                        'url' => route('student.info.department-show', $department),
                        'icon' => 'building'
                    ];
                });
            $results = $results->merge($departments);
        }

        if ($type === 'all' || $type === 'teachers') {
            $teachers = Teacher::whereHas('user', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->orWhere('specialization', 'like', "%{$query}%")
            ->with(['user', 'department'])
            ->limit(5)
            ->get()
            ->map(function ($teacher) {
                return [
                    'type' => 'teacher',
                    'title' => $teacher->user->name,
                    'description' => $teacher->department->name ?? 'No Department',
                    'url' => route('student.info.teacher-show', $teacher),
                    'icon' => 'user'
                ];
            });
            $results = $results->merge($teachers);
        }

        if ($type === 'all' || $type === 'staff') {
            $staff = Staff::whereHas('user', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->orWhere('position', 'like', "%{$query}%")
            ->with(['user', 'department'])
            ->limit(5)
            ->get()
            ->map(function ($staff) {
                return [
                    'type' => 'staff',
                    'title' => $staff->user->name,
                    'description' => $staff->position ?? 'Staff Member',
                    'url' => route('student.info.staff-show', $staff),
                    'icon' => 'briefcase'
                ];
            });
            $results = $results->merge($staff);
        }

        if ($type === 'all' || $type === 'courses') {
            $courses = Course::where('title', 'like', "%{$query}%")
                ->orWhere('course_code', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->with(['department', 'teacher.user'])
                ->limit(5)
                ->get()
                ->map(function ($course) {
                    return [
                        'type' => 'course',
                        'title' => $course->title,
                        'description' => $course->department->name ?? 'No Department',
                        'url' => route('student.courses.show', $course),
                        'icon' => 'book'
                    ];
                });
            $results = $results->merge($courses);
        }

        return view('student.info.search', compact('results', 'query', 'type'));
    }
}
