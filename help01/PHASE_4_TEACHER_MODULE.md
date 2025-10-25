# Phase 4: Teacher Module (Days 13-16)

## 🎯 **Phase 4 Objectives**
- Create teacher dashboard with course statistics
- Implement course management for teachers
- Build exam/quiz/assignment creation system
- Create result management system
- Implement notice viewing
- Build teacher profile management
- Create assessment grading system

---

## 📅 **Day 13: Teacher Dashboard & Course Management**

### **Step 1: Teacher Dashboard Controller**

**File: `app/Http/Controllers/Teacher/DashboardController.php`**

```php
<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\Result;
use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            abort(404, 'Teacher profile not found');
        }

        // Get teacher's courses with statistics
        $courses = Course::where('teacher_id', $teacher->id)
            ->where('is_active', true)
            ->with(['department', 'enrollments'])
            ->get();

        $courseStats = $courses->map(function ($course) {
            return [
                'course' => $course,
                'enrolled_count' => $course->enrollments()->where('status', 'enrolled')->count(),
                'total_exams' => $course->exams()->count(),
                'pending_results' => $course->exams()
                    ->whereHas('results', function($q) {
                        $q->where('is_published', false);
                    })
                    ->count(),
            ];
        });

        // Get upcoming exams
        $upcomingExams = Exam::whereHas('course', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->where('exam_date', '>=', now())
            ->where('status', 'scheduled')
            ->with('course')
            ->orderBy('exam_date')
            ->limit(5)
            ->get();

        // Get unpublished results
        $unpublishedResults = Result::whereHas('exam.course', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->where('is_published', false)
            ->with(['exam.course', 'student.user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get recent notices
        $recentNotices = Notice::where('is_published', true)
            ->where(function($query) {
                $query->whereJsonContains('target_roles', 'teacher')
                      ->orWhereJsonContains('target_roles', 'all');
            })
            ->where('publish_date', '<=', now())
            ->where(function($query) {
                $query->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>=', now());
            })
            ->with('user')
            ->latest()
            ->limit(5)
            ->get();

        // Dashboard statistics
        $stats = [
            'total_courses' => $courses->count(),
            'total_students' => $courses->sum(function($course) {
                return $course->enrollments()->where('status', 'enrolled')->count();
            }),
            'total_exams' => $courses->sum(function($course) {
                return $course->exams()->count();
            }),
            'pending_results' => $unpublishedResults->count(),
        ];

        return view('teacher.dashboard', compact(
            'teacher',
            'courseStats',
            'upcomingExams',
            'unpublishedResults',
            'recentNotices',
            'stats'
        ));
    }
}
```

### **Step 2: Teacher Course Management Controller**

**File: `app/Http/Controllers/Teacher/CourseController.php`**

```php
<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index()
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            abort(404, 'Teacher profile not found');
        }

        $courses = Course::where('teacher_id', $teacher->id)
            ->with(['department', 'enrollments'])
            ->withCount(['enrollments' => function($query) {
                $query->where('status', 'enrolled');
            }])
            ->latest()
            ->paginate(10);

        return view('teacher.courses.index', compact('courses'));
    }

    public function show(Course $course)
    {
        $teacher = Auth::user()->teacher;
        
        // Ensure teacher can only view their own courses
        if ($course->teacher_id !== $teacher->id) {
            abort(403, 'Unauthorized access to course.');
        }

        $course->load(['department', 'enrollments.student.user', 'exams']);
        
        $students = $course->enrollments()
            ->with('student.user')
            ->where('status', 'enrolled')
            ->get()
            ->pluck('student');

        $stats = [
            'total_enrollments' => $course->enrollments()->count(),
            'active_enrollments' => $course->enrollments()->where('status', 'enrolled')->count(),
            'completed_enrollments' => $course->enrollments()->where('status', 'completed')->count(),
            'total_exams' => $course->exams()->count(),
            'upcoming_exams' => $course->exams()
                ->where('exam_date', '>=', now())
                ->where('status', 'scheduled')
                ->count(),
        ];

        $recentExams = $course->exams()
            ->latest()
            ->limit(5)
            ->get();

        return view('teacher.courses.show', compact('course', 'students', 'stats', 'recentExams'));
    }

    public function students(Course $course)
    {
        $teacher = Auth::user()->teacher;
        
        // Ensure teacher can only view their own courses
        if ($course->teacher_id !== $teacher->id) {
            abort(403, 'Unauthorized access to course.');
        }

        $students = $course->enrollments()
            ->with('student.user')
            ->where('status', 'enrolled')
            ->paginate(20);

        return view('teacher.courses.students', compact('course', 'students'));
    }

    public function enrollStudent(Request $request, Course $course)
    {
        $teacher = Auth::user()->teacher;
        
        // Ensure teacher can only manage their own courses
        if ($course->teacher_id !== $teacher->id) {
            abort(403, 'Unauthorized access to course.');
        }

        $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        $student = Student::find($request->student_id);

        // Check if student is already enrolled
        if ($course->enrollments()->where('student_id', $student->id)->exists()) {
            return redirect()->back()
                ->with('error', 'Student is already enrolled in this course.');
        }

        // Check if course has available slots
        if ($course->enrollments()->where('status', 'enrolled')->count() >= $course->max_students) {
            return redirect()->back()
                ->with('error', 'Course is at full capacity.');
        }

        Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'enrolled',
            'enrollment_date' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Student enrolled successfully.');
    }

    public function removeStudent(Course $course, Student $student)
    {
        $teacher = Auth::user()->teacher;
        
        // Ensure teacher can only manage their own courses
        if ($course->teacher_id !== $teacher->id) {
            abort(403, 'Unauthorized access to course.');
        }

        $enrollment = $course->enrollments()->where('student_id', $student->id)->first();
        
        if ($enrollment) {
            $enrollment->update(['status' => 'dropped']);
            return redirect()->back()
                ->with('success', 'Student removed from course successfully.');
        }

        return redirect()->back()
            ->with('error', 'Student not found in this course.');
    }
}
```

### **Step 3: Teacher Dashboard View**

**File: `resources/views/teacher/dashboard.blade.php`**

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Teacher Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-2">Welcome, {{ Auth::user()->name }}!</h1>
                    <p class="text-gray-600">{{ $teacher->department->name ?? 'No Department' }} • {{ $teacher->employee_id }}</p>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-blue-100 text-sm">My Courses</p>
                            <p class="text-3xl font-bold mt-2">{{ $stats['total_courses'] }}</p>
                        </div>
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-green-100 text-sm">Total Students</p>
                            <p class="text-3xl font-bold mt-2">{{ $stats['total_students'] }}</p>
                        </div>
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-purple-100 text-sm">Total Exams</p>
                            <p class="text-3xl font-bold mt-2">{{ $stats['total_exams'] }}</p>
                        </div>
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-orange-100 text-sm">Pending Results</p>
                            <p class="text-3xl font-bold mt-2">{{ $stats['pending_results'] }}</p>
                        </div>
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-orange-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- My Courses -->
            <div class="bg-white rounded-lg shadow-sm mb-8">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800">My Courses</h2>
                    <a href="{{ route('teacher.courses.index') }}" class="text-blue-600 text-sm">View All</a>
                </div>
                <div class="p-6">
                    @if($courseStats->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($courseStats as $stat)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <h3 class="font-semibold text-gray-900">{{ $stat['course']->title }}</h3>
                                            <p class="text-sm text-gray-600">{{ $stat['course']->course_code }}</p>
                                            <p class="text-xs text-gray-500">{{ $stat['course']->department->name }}</p>
                                        </div>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ ucfirst($stat['course']->type) }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm text-gray-600 mb-3">
                                        <span>{{ $stat['enrolled_count'] }} students enrolled</span>
                                        <span>{{ $stat['total_exams'] }} exams</span>
                                    </div>
                                    @if($stat['pending_results'] > 0)
                                        <div class="mb-3">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                                {{ $stat['pending_results'] }} pending results
                                            </span>
                                        </div>
                                    @endif
                                    <div class="mt-3 flex space-x-2">
                                        <a href="{{ route('teacher.courses.show', $stat['course']->id) }}" 
                                           class="text-xs bg-blue-50 text-blue-600 px-3 py-1 rounded hover:bg-blue-100">
                                            View Details
                                        </a>
                                        <a href="{{ route('teacher.exams.index', ['course_id' => $stat['course']->id]) }}" 
                                           class="text-xs bg-purple-50 text-purple-600 px-3 py-1 rounded hover:bg-purple-100">
                                            Assessments
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <p class="text-gray-500">No courses assigned yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Quick Actions</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4">
                            <a href="{{ route('teacher.exams.create') }}" 
                               class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                                <svg class="w-6 h-6 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-700">Create Assessment</span>
                            </a>
                            
                            <a href="{{ route('teacher.courses.index') }}" 
                               class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                                <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-700">View My Courses</span>
                            </a>
                            
                            <a href="{{ route('teacher.results.index') }}" 
                               class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                                <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-700">Manage Results</span>
                            </a>
                            
                            <a href="{{ route('teacher.academic') }}" 
                               class="flex items-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                                <svg class="w-6 h-6 text-orange-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-700">Academic Info</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Recent Notices</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($recentNotices as $notice)
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $notice->title }}</p>
                                        <p class="text-sm text-gray-500">by {{ $notice->user->name }}</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <p class="text-xs text-gray-500">{{ $notice->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Exams -->
            @if($upcomingExams->count() > 0)
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-800">Upcoming Exams</h2>
                        <a href="{{ route('teacher.exams.index') }}" class="text-blue-600 text-sm">View All</a>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($upcomingExams as $exam)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex-1">
                                        <h3 class="font-medium text-gray-900">{{ $exam->title }}</h3>
                                        <p class="text-sm text-gray-600">{{ $exam->course->title }} • {{ ucfirst($exam->type) }}</p>
                                        <p class="text-xs text-gray-500">{{ $exam->exam_date->format('M d, Y') }} at {{ \Carbon\Carbon::parse($exam->start_time)->format('h:i A') }}</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ ucfirst($exam->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
```

---

## 📅 **Day 14: Exam Management System**

### **Step 1: Teacher Exam Controller**

**File: `app/Http/Controllers/Teacher/ExamController.php`**

```php
<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Course;
use App\Models\Result;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            abort(404, 'Teacher profile not found');
        }

        $query = Exam::whereHas('course', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->with('course');

        // Filter by course if specified
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        // Filter by type if specified
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $exams = $query->latest()->paginate(15);

        $courses = Course::where('teacher_id', $teacher->id)
            ->where('is_active', true)
            ->get();

        return view('teacher.exams.index', compact('exams', 'courses'));
    }

    public function create()
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            abort(404, 'Teacher profile not found');
        }

        $courses = Course::where('teacher_id', $teacher->id)
            ->where('is_active', true)
            ->with('department')
            ->get();

        return view('teacher.exams.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            abort(404, 'Teacher profile not found');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_id' => 'required|exists:courses,id',
            'type' => 'required|in:quiz,midterm,assignment',
            'exam_date' => 'required|date|after:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'total_marks' => 'required|integer|min:1|max:1000',
            'venue' => 'nullable|string|max:255',
        ]);

        // Ensure the course belongs to this teacher
        $course = Course::find($request->course_id);
        if ($course->teacher_id !== $teacher->id) {
            abort(403, 'Unauthorized access to course.');
        }

        Exam::create([
            'title' => $request->title,
            'description' => $request->description,
            'course_id' => $request->course_id,
            'type' => $request->type,
            'exam_date' => $request->exam_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'total_marks' => $request->total_marks,
            'venue' => $request->venue,
            'status' => 'scheduled',
        ]);

        return redirect()->route('teacher.exams.index')
            ->with('success', 'Assessment created successfully.');
    }

    public function show(Exam $exam)
    {
        $teacher = Auth::user()->teacher;
        
        // Ensure teacher can only view their own exams
        if ($exam->course->teacher_id !== $teacher->id) {
            abort(403, 'Unauthorized access to exam.');
        }

        $exam->load(['course.department', 'results.student.user']);
        
        $stats = [
            'total_students' => $exam->results()->count(),
            'average_marks' => $exam->results()->avg('marks_obtained') ?? 0,
            'highest_marks' => $exam->results()->max('marks_obtained') ?? 0,
            'lowest_marks' => $exam->results()->min('marks_obtained') ?? 0,
        ];

        return view('teacher.exams.show', compact('exam', 'stats'));
    }

    public function edit(Exam $exam)
    {
        $teacher = Auth::user()->teacher;
        
        // Ensure teacher can only edit their own exams
        if ($exam->course->teacher_id !== $teacher->id) {
            abort(403, 'Unauthorized access to exam.');
        }

        $courses = Course::where('teacher_id', $teacher->id)
            ->where('is_active', true)
            ->with('department')
            ->get();

        return view('teacher.exams.edit', compact('exam', 'courses'));
    }

    public function update(Request $request, Exam $exam)
    {
        $teacher = Auth::user()->teacher;
        
        // Ensure teacher can only edit their own exams
        if ($exam->course->teacher_id !== $teacher->id) {
            abort(403, 'Unauthorized access to exam.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_id' => 'required|exists:courses,id',
            'type' => 'required|in:quiz,midterm,assignment',
            'exam_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'total_marks' => 'required|integer|min:1|max:1000',
            'venue' => 'nullable|string|max:255',
            'status' => 'required|in:scheduled,ongoing,completed,cancelled',
        ]);

        $exam->update([
            'title' => $request->title,
            'description' => $request->description,
            'course_id' => $request->course_id,
            'type' => $request->type,
            'exam_date' => $request->exam_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'total_marks' => $request->total_marks,
            'venue' => $request->venue,
            'status' => $request->status,
        ]);

        return redirect()->route('teacher.exams.index')
            ->with('success', 'Assessment updated successfully.');
    }

    public function destroy(Exam $exam)
    {
        $teacher = Auth::user()->teacher;
        
        // Ensure teacher can only delete their own exams
        if ($exam->course->teacher_id !== $teacher->id) {
            abort(403, 'Unauthorized access to exam.');
        }

        $exam->delete();
        return redirect()->route('teacher.exams.index')
            ->with('success', 'Assessment deleted successfully.');
    }

    public function enterMarks(Exam $exam)
    {
        $teacher = Auth::user()->teacher;
        
        // Ensure teacher can only manage their own exams
        if ($exam->course->teacher_id !== $teacher->id) {
            abort(403, 'Unauthorized access to exam.');
        }

        $exam->load(['course.enrollments.student.user']);
        
        $students = $exam->course->enrollments()
            ->where('status', 'enrolled')
            ->with('student.user')
            ->get()
            ->pluck('student');

        $results = $exam->results()->with('student.user')->get()->keyBy('student_id');

        return view('teacher.exams.enter-marks', compact('exam', 'students', 'results'));
    }

    public function storeMarks(Request $request, Exam $exam)
    {
        $teacher = Auth::user()->teacher;
        
        // Ensure teacher can only manage their own exams
        if ($exam->course->teacher_id !== $teacher->id) {
            abort(403, 'Unauthorized access to exam.');
        }

        $request->validate([
            'marks' => 'required|array',
            'marks.*' => 'required|integer|min:0|max:' . $exam->total_marks,
        ]);

        foreach ($request->marks as $studentId => $marksObtained) {
            $grade = $this->calculateGrade($marksObtained, $exam->total_marks);
            $gradePoint = $this->calculateGradePoint($grade);

            Result::updateOrCreate(
                [
                    'exam_id' => $exam->id,
                    'student_id' => $studentId,
                ],
                [
                    'marks_obtained' => $marksObtained,
                    'grade' => $grade,
                    'grade_point' => $gradePoint,
                    'is_published' => false,
                ]
            );
        }

        return redirect()->route('teacher.exams.show', $exam)
            ->with('success', 'Marks entered successfully.');
    }

    private function calculateGrade($marksObtained, $totalMarks)
    {
        $percentage = ($marksObtained / $totalMarks) * 100;

        if ($percentage >= 80) return 'A+';
        if ($percentage >= 75) return 'A';
        if ($percentage >= 70) return 'A-';
        if ($percentage >= 65) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 55) return 'B-';
        if ($percentage >= 50) return 'C+';
        if ($percentage >= 45) return 'C';
        if ($percentage >= 40) return 'D';
        return 'F';
    }

    private function calculateGradePoint($grade)
    {
        return match($grade) {
            'A+' => 4.00,
            'A' => 3.75,
            'A-' => 3.50,
            'B+' => 3.25,
            'B' => 3.00,
            'B-' => 2.75,
            'C+' => 2.50,
            'C' => 2.25,
            'D' => 2.00,
            'F' => 0.00,
            default => 0.00
        };
    }
}
```

---

## 📅 **Day 15: Result Management & Profile**

### **Step 1: Teacher Result Controller**

**File: `app/Http/Controllers/Teacher/ResultController.php`**

```php
<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Result;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
{
    public function index()
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            abort(404, 'Teacher profile not found');
        }

        $results = Result::whereHas('exam.course', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->with(['exam.course', 'student.user'])
            ->latest()
            ->paginate(20);

        return view('teacher.results.index', compact('results'));
    }

    public function publish(Result $result)
    {
        $teacher = Auth::user()->teacher;
        
        // Ensure teacher can only publish their own results
        if ($result->exam->course->teacher_id !== $teacher->id) {
            abort(403, 'Unauthorized access to result.');
        }

        $result->update(['is_published' => true]);

        return redirect()->back()
            ->with('success', 'Result published successfully.');
    }

    public function unpublish(Result $result)
    {
        $teacher = Auth::user()->teacher;
        
        // Ensure teacher can only unpublish their own results
        if ($result->exam->course->teacher_id !== $teacher->id) {
            abort(403, 'Unauthorized access to result.');
        }

        $result->update(['is_published' => false]);

        return redirect()->back()
            ->with('success', 'Result unpublished successfully.');
    }

    public function publishAll(Exam $exam)
    {
        $teacher = Auth::user()->teacher;
        
        // Ensure teacher can only publish results for their own exams
        if ($exam->course->teacher_id !== $teacher->id) {
            abort(403, 'Unauthorized access to exam.');
        }

        $exam->results()->update(['is_published' => true]);

        return redirect()->back()
            ->with('success', 'All results published successfully.');
    }

    public function edit(Result $result)
    {
        $teacher = Auth::user()->teacher;
        
        // Ensure teacher can only edit their own results
        if ($result->exam->course->teacher_id !== $teacher->id) {
            abort(403, 'Unauthorized access to result.');
        }

        $result->load(['exam', 'student.user']);

        return view('teacher.results.edit', compact('result'));
    }

    public function update(Request $request, Result $result)
    {
        $teacher = Auth::user()->teacher;
        
        // Ensure teacher can only edit their own results
        if ($result->exam->course->teacher_id !== $teacher->id) {
            abort(403, 'Unauthorized access to result.');
        }

        $request->validate([
            'marks_obtained' => 'required|integer|min:0|max:' . $result->exam->total_marks,
            'remarks' => 'nullable|string|max:255',
        ]);

        $grade = $this->calculateGrade($request->marks_obtained, $result->exam->total_marks);
        $gradePoint = $this->calculateGradePoint($grade);

        $result->update([
            'marks_obtained' => $request->marks_obtained,
            'grade' => $grade,
            'grade_point' => $gradePoint,
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('teacher.results.index')
            ->with('success', 'Result updated successfully.');
    }

    private function calculateGrade($marksObtained, $totalMarks)
    {
        $percentage = ($marksObtained / $totalMarks) * 100;

        if ($percentage >= 80) return 'A+';
        if ($percentage >= 75) return 'A';
        if ($percentage >= 70) return 'A-';
        if ($percentage >= 65) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 55) return 'B-';
        if ($percentage >= 50) return 'C+';
        if ($percentage >= 45) return 'C';
        if ($percentage >= 40) return 'D';
        return 'F';
    }

    private function calculateGradePoint($grade)
    {
        return match($grade) {
            'A+' => 4.00,
            'A' => 3.75,
            'A-' => 3.50,
            'B+' => 3.25,
            'B' => 3.00,
            'B-' => 2.75,
            'C+' => 2.50,
            'C' => 2.25,
            'D' => 2.00,
            'F' => 0.00,
            default => 0.00
        };
    }
}
```

### **Step 2: Teacher Profile Controller**

**File: `app/Http/Controllers/Teacher/ProfileController.php`**

```php
<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            abort(404, 'Teacher profile not found');
        }

        $teacher->load(['user', 'department', 'courses.department']);

        $stats = [
            'total_courses' => $teacher->courses()->count(),
            'active_courses' => $teacher->courses()->where('is_active', true)->count(),
            'total_students' => $teacher->courses()
                ->withCount(['enrollments' => function($q) {
                    $q->where('status', 'enrolled');
                }])
                ->get()
                ->sum('enrollments_count'),
        ];

        return view('teacher.profile.index', compact('teacher', 'stats'));
    }

    public function edit()
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            abort(404, 'Teacher profile not found');
        }

        $teacher->load(['user', 'department']);

        return view('teacher.profile.edit', compact('teacher'));
    }

    public function update(Request $request)
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            abort(404, 'Teacher profile not found');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $teacher->user_id,
            'qualification' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Update user information
        $teacher->user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old image
            if ($teacher->user->profile_image) {
                Storage::disk('public')->delete($teacher->user->profile_image);
            }
            
            $profileImagePath = $request->file('profile_image')->store('profile-images', 'public');
            $teacher->user->update(['profile_image' => $profileImagePath]);
        }

        // Update teacher information
        $teacher->update([
            'qualification' => $request->qualification,
            'specialization' => $request->specialization,
            'bio' => $request->bio,
        ]);

        return redirect()->route('teacher.profile.index')
            ->with('success', 'Profile updated successfully.');
    }

    public function changePassword()
    {
        return view('teacher.profile.change-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('teacher.profile.index')
            ->with('success', 'Password updated successfully.');
    }
}
```

---

## 📅 **Day 16: Teacher Routes & Views**

### **Step 1: Teacher Routes**

**File: `routes/web.php` (Teacher section)**

```php
// Teacher Routes
Route::prefix('teacher')->name('teacher.')->middleware(['auth', 'role:teacher', 'prevent.back'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Teacher\DashboardController::class, 'index'])->name('dashboard');
    
    // Profile routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [App\Http\Controllers\Teacher\ProfileController::class, 'index'])->name('profile.index');
        Route::get('/edit', [App\Http\Controllers\Teacher\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/update', [App\Http\Controllers\Teacher\ProfileController::class, 'update'])->name('profile.update');
        Route::get('/change-password', [App\Http\Controllers\Teacher\ProfileController::class, 'changePassword'])->name('profile.change-password');
        Route::put('/update-password', [App\Http\Controllers\Teacher\ProfileController::class, 'updatePassword'])->name('profile.update-password');
    });
    
    // Course routes
    Route::prefix('courses')->group(function () {
        Route::get('/', [App\Http\Controllers\Teacher\CourseController::class, 'index'])->name('courses.index');
        Route::get('/{course}', [App\Http\Controllers\Teacher\CourseController::class, 'show'])->name('courses.show');
        Route::get('/{course}/students', [App\Http\Controllers\Teacher\CourseController::class, 'students'])->name('courses.students');
        Route::post('/{course}/enroll-student', [App\Http\Controllers\Teacher\CourseController::class, 'enrollStudent'])->name('courses.enroll-student');
        Route::delete('/{course}/students/{student}', [App\Http\Controllers\Teacher\CourseController::class, 'removeStudent'])->name('courses.remove-student');
    });
    
    // Exam routes
    Route::prefix('exams')->group(function () {
        Route::get('/', [App\Http\Controllers\Teacher\ExamController::class, 'index'])->name('exams.index');
        Route::get('/create', [App\Http\Controllers\Teacher\ExamController::class, 'create'])->name('exams.create');
        Route::post('/', [App\Http\Controllers\Teacher\ExamController::class, 'store'])->name('exams.store');
        Route::get('/{exam}', [App\Http\Controllers\Teacher\ExamController::class, 'show'])->name('exams.show');
        Route::get('/{exam}/edit', [App\Http\Controllers\Teacher\ExamController::class, 'edit'])->name('exams.edit');
        Route::put('/{exam}', [App\Http\Controllers\Teacher\ExamController::class, 'update'])->name('exams.update');
        Route::delete('/{exam}', [App\Http\Controllers\Teacher\ExamController::class, 'destroy'])->name('exams.destroy');
        Route::get('/{exam}/enter-marks', [App\Http\Controllers\Teacher\ExamController::class, 'enterMarks'])->name('exams.enter-marks');
        Route::post('/{exam}/store-marks', [App\Http\Controllers\Teacher\ExamController::class, 'storeMarks'])->name('exams.store-marks');
    });
    
    // Result routes
    Route::prefix('results')->group(function () {
        Route::get('/', [App\Http\Controllers\Teacher\ResultController::class, 'index'])->name('results.index');
        Route::get('/{result}/edit', [App\Http\Controllers\Teacher\ResultController::class, 'edit'])->name('results.edit');
        Route::put('/{result}', [App\Http\Controllers\Teacher\ResultController::class, 'update'])->name('results.update');
        Route::patch('/{result}/publish', [App\Http\Controllers\Teacher\ResultController::class, 'publish'])->name('results.publish');
        Route::patch('/{result}/unpublish', [App\Http\Controllers\Teacher\ResultController::class, 'unpublish'])->name('results.unpublish');
        Route::patch('/exams/{exam}/publish-all', [App\Http\Controllers\Teacher\ResultController::class, 'publishAll'])->name('results.publish-all');
    });
    
    // Academic routes
    Route::get('/academic', function () {
        $teacher = Auth::user()->teacher;
        $teacher->load(['department', 'courses.department']);
        return view('teacher.academic', compact('teacher'));
    })->name('academic');
});
```

### **Step 2: Git Commit**

```bash
git add .
git commit -m "Phase 4 complete: Teacher module with course and exam management"
```

---

## ✅ **Phase 4 Checklist**

- [x] Teacher dashboard with statistics
- [x] Course management for teachers
- [x] Exam/quiz/assignment creation
- [x] Result management system
- [x] Notice viewing
- [x] Teacher profile management
- [x] Assessment grading system
- [x] Student enrollment management
- [x] Grade calculation system
- [x] Result publishing system
- [x] All teacher routes configured
- [x] Authorization checks implemented

---

## 🚀 **Next Steps**

Phase 4 is complete! You now have:
- Complete teacher dashboard
- Course management system
- Exam creation and management
- Result entry and grading
- Student enrollment management
- Profile management
- Grade calculation system

**Ready for Phase 5?** We'll build the Student module with enrollment and payment systems! 🎯
