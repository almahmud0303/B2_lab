# Phase 5: Student Module (Days 17-20)

## 🎯 **Phase 5 Objectives**
- Create student dashboard with academic overview
- Implement course enrollment system
- Build fee payment system with bKash integration
- Create result viewing system
- Implement notice viewing
- Build student profile management
- Create payment history tracking

---

## 📅 **Day 17: Student Dashboard & Course Enrollment**

### **Step 1: Student Dashboard Controller**

**File: `app/Http/Controllers/Student/DashboardController.php`**

```php
<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\Result;
use App\Models\Notice;
use App\Models\Fee;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Get student's enrollments with course details
        $enrollments = Enrollment::where('student_id', $student->id)
            ->where('status', 'enrolled')
            ->with(['course.department', 'course.teacher.user'])
            ->get();

        // Get upcoming exams
        $upcomingExams = Exam::whereHas('course.enrollments', function($query) use ($student) {
                $query->where('student_id', $student->id)
                      ->where('status', 'enrolled');
            })
            ->where('exam_date', '>=', now())
            ->where('status', 'scheduled')
            ->with('course')
            ->orderBy('exam_date')
            ->limit(5)
            ->get();

        // Get recent results
        $recentResults = Result::where('student_id', $student->id)
            ->where('is_published', true)
            ->with(['exam.course'])
            ->latest()
            ->limit(5)
            ->get();

        // Get recent notices
        $recentNotices = Notice::where('is_published', true)
            ->where(function($query) {
                $query->whereJsonContains('target_roles', 'student')
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

        // Get fee information
        $pendingFees = Fee::where('student_id', $student->id)
            ->whereIn('status', ['pending', 'partial', 'overdue'])
            ->latest()
            ->limit(3)
            ->get();

        // Dashboard statistics
        $stats = [
            'total_courses' => $enrollments->count(),
            'completed_courses' => Enrollment::where('student_id', $student->id)
                ->where('status', 'completed')
                ->count(),
            'total_exams' => Exam::whereHas('course.enrollments', function($query) use ($student) {
                $query->where('student_id', $student->id)
                      ->where('status', 'enrolled');
            })->count(),
            'published_results' => Result::where('student_id', $student->id)
                ->where('is_published', true)
                ->count(),
            'pending_fees' => $pendingFees->count(),
            'total_paid' => Payment::where('student_id', $student->id)
                ->where('status', 'completed')
                ->sum('amount'),
        ];

        return view('student.dashboard', compact(
            'student',
            'enrollments',
            'upcomingExams',
            'recentResults',
            'recentNotices',
            'pendingFees',
            'stats'
        ));
    }
}
```

### **Step 2: Student Course Controller**

**File: `app/Http/Controllers/Student/CourseController.php`**

```php
<?php

namespace App\Http\Controllers\Student;

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
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        $enrollments = Enrollment::where('student_id', $student->id)
            ->with(['course.department', 'course.teacher.user'])
            ->latest()
            ->paginate(10);

        return view('student.courses.index', compact('enrollments'));
    }

    public function available()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Get courses from student's department that are not enrolled
        $enrolledCourseIds = Enrollment::where('student_id', $student->id)
            ->pluck('course_id')
            ->toArray();

        $availableCourses = Course::where('department_id', $student->department_id)
            ->where('is_active', true)
            ->whereNotIn('id', $enrolledCourseIds)
            ->with(['department', 'teacher.user'])
            ->withCount(['enrollments' => function($query) {
                $query->where('status', 'enrolled');
            }])
            ->latest()
            ->paginate(12);

        return view('student.courses.available', compact('availableCourses'));
    }

    public function show(Course $course)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Check if student is enrolled in this course
        $enrollment = Enrollment::where('student_id', $student->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            abort(403, 'You are not enrolled in this course.');
        }

        $course->load(['department', 'teacher.user', 'exams.results' => function($query) use ($student) {
            $query->where('student_id', $student->id);
        }]);

        $stats = [
            'total_exams' => $course->exams()->count(),
            'completed_exams' => $course->exams()
                ->whereHas('results', function($q) use ($student) {
                    $q->where('student_id', $student->id)
                      ->where('is_published', true);
                })
                ->count(),
            'average_marks' => $course->exams()
                ->whereHas('results', function($q) use ($student) {
                    $q->where('student_id', $student->id)
                      ->where('is_published', true);
                })
                ->with('results')
                ->get()
                ->pluck('results')
                ->flatten()
                ->where('student_id', $student->id)
                ->avg('marks_obtained') ?? 0,
        ];

        $upcomingExams = $course->exams()
            ->where('exam_date', '>=', now())
            ->where('status', 'scheduled')
            ->orderBy('exam_date')
            ->get();

        $recentResults = $course->exams()
            ->whereHas('results', function($q) use ($student) {
                $q->where('student_id', $student->id)
                  ->where('is_published', true);
            })
            ->with(['results' => function($q) use ($student) {
                $q->where('student_id', $student->id);
            }])
            ->latest()
            ->limit(5)
            ->get();

        return view('student.courses.show', compact('course', 'enrollment', 'stats', 'upcomingExams', 'recentResults'));
    }

    public function enroll(Course $course)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Check if course belongs to student's department
        if ($course->department_id !== $student->department_id) {
            abort(403, 'You can only enroll in courses from your department.');
        }

        // Check if student is already enrolled
        if (Enrollment::where('student_id', $student->id)
            ->where('course_id', $course->id)
            ->exists()) {
            return redirect()->back()
                ->with('error', 'You are already enrolled in this course.');
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

        return redirect()->route('student.courses.index')
            ->with('success', 'Successfully enrolled in ' . $course->title);
    }

    public function drop(Course $course)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        $enrollment = Enrollment::where('student_id', $student->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return redirect()->back()
                ->with('error', 'You are not enrolled in this course.');
        }

        $enrollment->update(['status' => 'dropped']);

        return redirect()->route('student.courses.index')
            ->with('success', 'Successfully dropped ' . $course->title);
    }
}
```

### **Step 3: Student Dashboard View**

**File: `resources/views/student/dashboard.blade.php`**

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Student Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-2">Welcome, {{ Auth::user()->name }}!</h1>
                    <p class="text-gray-600">{{ $student->student_id }} • {{ $student->department->name ?? 'No Department' }}</p>
                    @if($student->hall)
                        <p class="text-sm text-gray-500 mt-1">Hall: {{ $student->hall->name }}</p>
                    @endif
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-blue-100 text-sm">Enrolled Courses</p>
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
                            <p class="text-green-100 text-sm">Completed Courses</p>
                            <p class="text-3xl font-bold mt-2">{{ $stats['completed_courses'] }}</p>
                        </div>
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-purple-100 text-sm">Published Results</p>
                            <p class="text-3xl font-bold mt-2">{{ $stats['published_results'] }}</p>
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
                            <p class="text-orange-100 text-sm">Total Paid</p>
                            <p class="text-3xl font-bold mt-2">TK {{ number_format($stats['total_paid'], 2) }}</p>
                        </div>
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-orange-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- My Courses -->
            <div class="bg-white rounded-lg shadow-sm mb-8">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800">My Courses</h2>
                    <a href="{{ route('student.courses.index') }}" class="text-blue-600 text-sm">View All</a>
                </div>
                <div class="p-6">
                    @if($enrollments->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($enrollments as $enrollment)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <h3 class="font-semibold text-gray-900">{{ $enrollment->course->title }}</h3>
                                            <p class="text-sm text-gray-600">{{ $enrollment->course->course_code }}</p>
                                            <p class="text-xs text-gray-500">{{ $enrollment->course->department->name }}</p>
                                        </div>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ ucfirst($enrollment->status) }}
                                        </span>
                                    </div>
                                    <div class="text-sm text-gray-600 mb-3">
                                        <p>Teacher: {{ $enrollment->course->teacher->user->name ?? 'Not Assigned' }}</p>
                                        <p>Credits: {{ $enrollment->course->credits }}</p>
                                    </div>
                                    <div class="mt-3 flex space-x-2">
                                        <a href="{{ route('student.courses.show', $enrollment->course->id) }}" 
                                           class="text-xs bg-blue-50 text-blue-600 px-3 py-1 rounded hover:bg-blue-100">
                                            View Details
                                        </a>
                                        <a href="{{ route('student.results.index', ['course_id' => $enrollment->course->id]) }}" 
                                           class="text-xs bg-purple-50 text-purple-600 px-3 py-1 rounded hover:bg-purple-100">
                                            Results
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
                            <p class="text-gray-500 mb-4">No courses enrolled yet.</p>
                            <a href="{{ route('student.courses.available') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                Browse Available Courses
                            </a>
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
                            <a href="{{ route('student.courses.available') }}" 
                               class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                                <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-700">Browse Courses</span>
                            </a>
                            
                            <a href="{{ route('student.payments.history') }}" 
                               class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                                <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-700">Payment History</span>
                            </a>
                            
                            <a href="{{ route('student.results.index') }}" 
                               class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                                <svg class="w-6 h-6 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-700">View Results</span>
                            </a>
                            
                            <a href="{{ route('student.profile.index') }}" 
                               class="flex items-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                                <svg class="w-6 h-6 text-orange-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-700">My Profile</span>
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
                        <a href="{{ route('student.exams.index') }}" class="text-blue-600 text-sm">View All</a>
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

## 📅 **Day 18: Payment System with bKash Integration**

### **Step 1: Payment Controller**

**File: `app/Http/Controllers/Student/PaymentController.php`**

```php
<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Course;
use App\Models\Fee;
use App\Services\BkashPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $bkashService;

    public function __construct(BkashPaymentService $bkashService)
    {
        $this->bkashService = $bkashService;
    }

    public function history()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        $payments = Payment::where('student_id', $student->id)
            ->with(['course', 'fee'])
            ->latest()
            ->paginate(15);

        return view('student.payments.history', compact('payments'));
    }

    public function show(Payment $payment)
    {
        $student = Auth::user()->student;
        
        // Ensure student can only view their own payments
        if ($payment->student_id !== $student->id) {
            abort(403, 'Unauthorized access to payment.');
        }

        $payment->load(['course', 'fee']);

        return view('student.payments.show', compact('payment'));
    }

    public function create(Course $course)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Check if student is enrolled in this course
        $enrollment = $student->enrollments()
            ->where('course_id', $course->id)
            ->where('status', 'enrolled')
            ->first();

        if (!$enrollment) {
            abort(403, 'You are not enrolled in this course.');
        }

        return view('student.payments.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $course->currency,
            'payment_method' => 'required|in:cash,bank_transfer,mobile_banking,bkash,nagad,rocket',
        ]);

        // Check if student is enrolled in this course
        $enrollment = $student->enrollments()
            ->where('course_id', $course->id)
            ->where('status', 'enrolled')
            ->first();

        if (!$enrollment) {
            abort(403, 'You are not enrolled in this course.');
        }

        $payment = Payment::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'status' => 'pending',
            'notes' => 'Course fee payment for ' . $course->title,
        ]);

        // Handle different payment methods
        if (in_array($request->payment_method, ['bkash', 'nagad', 'rocket'])) {
            return $this->processMobilePayment($payment);
        }

        return redirect()->route('student.payments.instructions', $payment);
    }

    private function processMobilePayment(Payment $payment)
    {
        try {
            if ($payment->payment_method === 'bkash') {
                $response = $this->bkashService->createPayment($payment);
                
                if ($response['status'] === 'success') {
                    $payment->update([
                        'transaction_id' => $response['paymentID'],
                        'payment_details' => $response,
                    ]);

                    return redirect($response['bkashURL']);
                } else {
                    $payment->update(['status' => 'failed']);
                    return redirect()->back()
                        ->with('error', 'Payment initiation failed: ' . $response['message']);
                }
            } else {
                // For other mobile banking methods, redirect to instructions
                return redirect()->route('student.payments.instructions', $payment);
            }
        } catch (\Exception $e) {
            Log::error('Payment processing error: ' . $e->getMessage());
            $payment->update(['status' => 'failed']);
            
            return redirect()->back()
                ->with('error', 'Payment processing failed. Please try again.');
        }
    }

    public function instructions(Payment $payment)
    {
        $student = Auth::user()->student;
        
        // Ensure student can only view their own payments
        if ($payment->student_id !== $student->id) {
            abort(403, 'Unauthorized access to payment.');
        }

        return view('student.payments.instructions', compact('payment'));
    }

    public function success(Payment $payment)
    {
        $student = Auth::user()->student;
        
        // Ensure student can only view their own payments
        if ($payment->student_id !== $student->id) {
            abort(403, 'Unauthorized access to payment.');
        }

        // Handle bKash callback
        if ($payment->payment_method === 'bkash' && $payment->transaction_id) {
            try {
                $response = $this->bkashService->executePayment($payment->transaction_id);
                
                if ($response['status'] === 'success') {
                    $payment->update([
                        'status' => 'completed',
                        'payment_details' => array_merge($payment->payment_details ?? [], $response),
                    ]);

                    // Create or update fee record
                    $this->updateFeeRecord($payment);
                } else {
                    $payment->update(['status' => 'failed']);
                }
            } catch (\Exception $e) {
                Log::error('Payment execution error: ' . $e->getMessage());
                $payment->update(['status' => 'failed']);
            }
        }

        return view('student.payments.success', compact('payment'));
    }

    public function failed(Payment $payment)
    {
        $student = Auth::user()->student;
        
        // Ensure student can only view their own payments
        if ($payment->student_id !== $student->id) {
            abort(403, 'Unauthorized access to payment.');
        }

        $payment->update(['status' => 'failed']);

        return view('student.payments.failed', compact('payment'));
    }

    private function updateFeeRecord(Payment $payment)
    {
        // Find or create fee record for this course
        $fee = Fee::firstOrCreate(
            [
                'student_id' => $payment->student_id,
                'fee_type' => 'course_fee',
            ],
            [
                'amount' => $payment->course->currency,
                'due_date' => now()->addDays(30),
                'status' => 'pending',
            ]
        );

        // Update paid amount
        $newPaidAmount = $fee->paid_amount + $payment->amount;
        $fee->update([
            'paid_amount' => $newPaidAmount,
            'status' => $newPaidAmount >= $fee->amount ? 'paid' : 'partial',
            'paid_date' => $newPaidAmount >= $fee->amount ? now() : $fee->paid_date,
        ]);

        // Link payment to fee
        $payment->update(['fee_id' => $fee->id]);
    }
}
```

### **Step 2: bKash Payment Service**

**File: `app/Services/BkashPaymentService.php`**

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BkashPaymentService
{
    protected $baseUrl;
    protected $appKey;
    protected $appSecret;
    protected $username;
    protected $password;

    public function __construct()
    {
        $this->baseUrl = config('services.bkash.base_url', 'https://tokenized.pay.bka.sh/v1.2.0-beta');
        $this->appKey = config('services.bkash.app_key');
        $this->appSecret = config('services.bkash.app_secret');
        $this->username = config('services.bkash.username');
        $this->password = config('services.bkash.password');
    }

    public function createPayment($payment)
    {
        try {
            // Get access token
            $token = $this->getAccessToken();
            if (!$token) {
                return ['status' => 'error', 'message' => 'Failed to get access token'];
            }

            // Create payment
            $response = Http::withHeaders([
                'Authorization' => $token,
                'X-APP-Key' => $this->appKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/tokenized/checkout/payment/create', [
                'mode' => '0011',
                'payerReference' => $payment->student_id,
                'callbackURL' => route('student.payments.success', $payment),
                'amount' => $payment->amount,
                'currency' => 'BDT',
                'intent' => 'sale',
                'merchantInvoiceNumber' => 'INV-' . $payment->id,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if ($data['statusCode'] === '0000') {
                    return [
                        'status' => 'success',
                        'paymentID' => $data['paymentID'],
                        'bkashURL' => $data['bkashURL'],
                    ];
                }
            }

            return ['status' => 'error', 'message' => 'Payment creation failed'];
        } catch (\Exception $e) {
            Log::error('bKash payment creation error: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'Payment service error'];
        }
    }

    public function executePayment($paymentId)
    {
        try {
            $token = $this->getAccessToken();
            if (!$token) {
                return ['status' => 'error', 'message' => 'Failed to get access token'];
            }

            $response = Http::withHeaders([
                'Authorization' => $token,
                'X-APP-Key' => $this->appKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/tokenized/checkout/payment/execute/' . $paymentId);

            if ($response->successful()) {
                $data = $response->json();
                if ($data['statusCode'] === '0000') {
                    return [
                        'status' => 'success',
                        'transactionID' => $data['trxID'],
                        'amount' => $data['amount'],
                        'currency' => $data['currency'],
                    ];
                }
            }

            return ['status' => 'error', 'message' => 'Payment execution failed'];
        } catch (\Exception $e) {
            Log::error('bKash payment execution error: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'Payment service error'];
        }
    }

    private function getAccessToken()
    {
        try {
            $response = Http::withHeaders([
                'username' => $this->username,
                'password' => $this->password,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/tokenized/checkout/token/grant', [
                'app_key' => $this->appKey,
                'app_secret' => $this->appSecret,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if ($data['statusCode'] === '0000') {
                    return $data['id_token'];
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('bKash token error: ' . $e->getMessage());
            return null;
        }
    }
}
```

---

## 📅 **Day 19: Result Viewing & Profile Management**

### **Step 1: Student Result Controller**

**File: `app/Http/Controllers/Student/ResultController.php`**

```php
<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Result;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        $query = Result::where('student_id', $student->id)
            ->where('is_published', true)
            ->with(['exam.course']);

        // Filter by course if specified
        if ($request->filled('course_id')) {
            $query->whereHas('exam', function($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }

        $results = $query->latest()->paginate(15);

        $courses = $student->courses()->get();

        return view('student.results.index', compact('results', 'courses'));
    }

    public function show(Result $result)
    {
        $student = Auth::user()->student;
        
        // Ensure student can only view their own results
        if ($result->student_id !== $student->id) {
            abort(403, 'Unauthorized access to result.');
        }

        $result->load(['exam.course', 'student.user']);

        return view('student.results.show', compact('result'));
    }

    public function transcript()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        $results = Result::where('student_id', $student->id)
            ->where('is_published', true)
            ->with(['exam.course'])
            ->get()
            ->groupBy('exam.course_id');

        $courses = $student->courses()->get();
        
        $stats = [
            'total_courses' => $courses->count(),
            'completed_courses' => $results->count(),
            'total_credits' => $courses->sum('credits'),
            'completed_credits' => $results->sum(function($courseResults) {
                return $courseResults->first()->exam->course->credits;
            }),
            'cgpa' => $this->calculateCGPA($results),
        ];

        return view('student.results.transcript', compact('results', 'courses', 'stats'));
    }

    private function calculateCGPA($results)
    {
        $totalGradePoints = 0;
        $totalCredits = 0;

        foreach ($results as $courseResults) {
            $course = $courseResults->first()->exam->course;
            $credits = $course->credits;
            
            // Get the latest result for this course
            $latestResult = $courseResults->sortByDesc('created_at')->first();
            
            $totalGradePoints += $latestResult->grade_point * $credits;
            $totalCredits += $credits;
        }

        return $totalCredits > 0 ? round($totalGradePoints / $totalCredits, 2) : 0;
    }
}
```

### **Step 2: Student Profile Controller**

**File: `app/Http/Controllers/Student/ProfileController.php`**

```php
<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        $student->load(['user', 'department', 'hall', 'enrollments.course']);

        $stats = [
            'total_courses' => $student->enrollments()->count(),
            'completed_courses' => $student->enrollments()->where('status', 'completed')->count(),
            'active_courses' => $student->enrollments()->where('status', 'enrolled')->count(),
            'cgpa' => $student->cgpa ?? 0,
        ];

        return view('student.profile.index', compact('student', 'stats'));
    }

    public function edit()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        $student->load(['user', 'department', 'hall']);

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
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Update user information
        $student->user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old image
            if ($student->user->profile_image) {
                Storage::disk('public')->delete($student->user->profile_image);
            }
            
            $profileImagePath = $request->file('profile_image')->store('profile-images', 'public');
            $student->user->update(['profile_image' => $profileImagePath]);
        }

        return redirect()->route('student.profile.index')
            ->with('success', 'Profile updated successfully.');
    }

    public function changePassword()
    {
        return view('student.profile.change-password');
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

        return redirect()->route('student.profile.index')
            ->with('success', 'Password updated successfully.');
    }
}
```

---

## 📅 **Day 20: Student Routes & Configuration**

### **Step 1: Student Routes**

**File: `routes/web.php` (Student section)**

```php
// Student Routes
Route::prefix('student')->name('student.')->middleware(['auth', 'role:student', 'prevent.back'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Student\DashboardController::class, 'index'])->name('dashboard');
    
    // Profile routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [App\Http\Controllers\Student\ProfileController::class, 'index'])->name('profile.index');
        Route::get('/edit', [App\Http\Controllers\Student\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/update', [App\Http\Controllers\Student\ProfileController::class, 'update'])->name('profile.update');
        Route::get('/change-password', [App\Http\Controllers\Student\ProfileController::class, 'changePassword'])->name('profile.change-password');
        Route::put('/update-password', [App\Http\Controllers\Student\ProfileController::class, 'updatePassword'])->name('profile.update-password');
    });
    
    // Course routes
    Route::prefix('courses')->group(function () {
        Route::get('/', [App\Http\Controllers\Student\CourseController::class, 'index'])->name('courses.index');
        Route::get('/available', [App\Http\Controllers\Student\CourseController::class, 'available'])->name('courses.available');
        Route::get('/{course}', [App\Http\Controllers\Student\CourseController::class, 'show'])->name('courses.show');
        Route::post('/{course}/enroll', [App\Http\Controllers\Student\CourseController::class, 'enroll'])->name('courses.enroll');
        Route::delete('/{course}/drop', [App\Http\Controllers\Student\CourseController::class, 'drop'])->name('courses.drop');
    });
    
    // Payment routes
    Route::prefix('payments')->group(function () {
        Route::get('/history', [App\Http\Controllers\Student\PaymentController::class, 'history'])->name('payments.history');
        Route::get('/{paymentId}', [App\Http\Controllers\Student\PaymentController::class, 'show'])->name('payments.show');
        Route::get('/course/{courseId}/create', [App\Http\Controllers\Student\PaymentController::class, 'create'])->name('payments.create');
        Route::post('/course/{courseId}/store', [App\Http\Controllers\Student\PaymentController::class, 'store'])->name('payments.store');
        Route::get('/{paymentId}/instructions', [App\Http\Controllers\Student\PaymentController::class, 'instructions'])->name('payments.instructions');
        Route::get('/{paymentId}/success', [App\Http\Controllers\Student\PaymentController::class, 'success'])->name('payments.success');
        Route::get('/{paymentId}/failed', [App\Http\Controllers\Student\PaymentController::class, 'failed'])->name('payments.failed');
    });
    
    // Result routes
    Route::prefix('results')->group(function () {
        Route::get('/', [App\Http\Controllers\Student\ResultController::class, 'index'])->name('results.index');
        Route::get('/{result}', [App\Http\Controllers\Student\ResultController::class, 'show'])->name('results.show');
        Route::get('/transcript', [App\Http\Controllers\Student\ResultController::class, 'transcript'])->name('results.transcript');
    });
    
    // Notice routes
    Route::get('/notices', function () {
        $student = Auth::user()->student;
        $notices = \App\Models\Notice::where('is_published', true)
            ->where(function($query) {
                $query->whereJsonContains('target_roles', 'student')
                      ->orWhereJsonContains('target_roles', 'all');
            })
            ->where('publish_date', '<=', now())
            ->where(function($query) {
                $query->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>=', now());
            })
            ->with('user')
            ->latest()
            ->paginate(15);
        
        return view('student.notices.index', compact('notices'));
    })->name('notices.index');
});
```

### **Step 2: bKash Configuration**

**File: `config/services.php`**

```php
'bkash' => [
    'base_url' => env('BKASH_BASE_URL', 'https://tokenized.pay.bka.sh/v1.2.0-beta'),
    'app_key' => env('BKASH_APP_KEY'),
    'app_secret' => env('BKASH_APP_SECRET'),
    'username' => env('BKASH_USERNAME'),
    'password' => env('BKASH_PASSWORD'),
],
```

### **Step 3: Environment Variables**

**File: `.env` (add these lines)**

```env
# bKash Payment Configuration
BKASH_BASE_URL=https://tokenized.pay.bka.sh/v1.2.0-beta
BKASH_APP_KEY=your_bkash_app_key
BKASH_APP_SECRET=your_bkash_app_secret
BKASH_USERNAME=your_bkash_username
BKASH_PASSWORD=your_bkash_password
```

### **Step 4: Git Commit**

```bash
git add .
git commit -m "Phase 5 complete: Student module with enrollment and payment system"
```

---

## ✅ **Phase 5 Checklist**

- [x] Student dashboard with academic overview
- [x] Course enrollment system
- [x] Fee payment system
- [x] bKash mobile banking integration
- [x] Result viewing system
- [x] Notice viewing
- [x] Student profile management
- [x] Payment history tracking
- [x] Academic transcript
- [x] Course browsing and enrollment
- [x] Payment instructions and success pages
- [x] All student routes configured
- [x] Authorization checks implemented

---

## 🚀 **Next Steps**

Phase 5 is complete! You now have:
- Complete student dashboard
- Course enrollment system
- Payment system with bKash integration
- Result viewing and transcript
- Profile management
- Notice viewing system
- Payment history tracking

**Ready for Phase 6?** We'll build the Staff and Department Head modules! 🎯
