# 📅 UMS Development Guide - Days 11-20

## 🎯 **DAYS 11-20: ADVANCED FEATURES & DEPLOYMENT**

Complete guide for Teacher Module, Staff Module, Department Head features, and final deployment.

---

# 📅 **DAY 11: TEACHER MODULE - PART 1**

## **Goals:**
- ✅ Create teacher dashboard
- ✅ Implement course management
- ✅ Setup attendance marking system

## **Time Estimate:** 5-6 hours

---

## **Step 11.1: Create Feature Branch** (5 mins)

```bash
git checkout develop
git pull origin develop
git checkout -b feature/teacher-module
```

---

## **Step 11.2: Create Teacher Controllers** (20 mins)

```bash
php artisan make:controller Teacher/DashboardController
php artisan make:controller Teacher/ProfileController
php artisan make:controller Teacher/CourseController --resource
php artisan make:controller Teacher/AttendanceController
```

**Why separate controllers:**
- Each handles specific functionality
- Easier to maintain
- Follows Single Responsibility Principle

**Commit:**
```bash
git add app/Http/Controllers/Teacher/
git commit -m "feat: create teacher module controller structure"
```

---

## **Step 11.3: Teacher Dashboard** (45 mins)

**File:** `app/Http/Controllers/Teacher/DashboardController.php`

**Code:**
```php
<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\{Course, Attendance, Exam, Result};
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $teacher = Auth::user()->teacher;

        if (!$teacher) {
            abort(404, 'Teacher profile not found');
        }

        // Course statistics
        $totalCourses = $teacher->courses()->count();
        $activeCourses = $teacher->courses()
            ->where('is_active', true)
            ->count();

        // Get courses with student count
        $courses = $teacher->courses()
            ->withCount('enrollments')
            ->with('department')
            ->where('is_active', true)
            ->get();

        // Calculate total students
        $totalStudents = $courses->sum('enrollments_count');

        // Upcoming exams
        $upcomingExams = Exam::whereIn('course_id', $courses->pluck('id'))
            ->where('exam_date', '>=', now())
            ->with('course')
            ->orderBy('exam_date', 'asc')
            ->take(5)
            ->get();

        // Recent attendance
        $recentAttendance = Attendance::whereIn('course_id', $courses->pluck('id'))
            ->with(['course', 'student.user'])
            ->orderBy('date', 'desc')
            ->take(10)
            ->get();

        return view('teacher.dashboard', compact(
            'teacher',
            'totalCourses',
            'activeCourses',
            'totalStudents',
            'courses',
            'upcomingExams',
            'recentAttendance'
        ));
    }
}
```

**Explanation:**

1. **`withCount('enrollments')`**
   ```php
   // Adds enrollments_count to each course
   $course->enrollments_count  // Number of students enrolled
   ```

2. **`whereIn('course_id', $courses->pluck('id'))`**
   ```php
   // Gets exams for teacher's courses only
   $courses->pluck('id')  // [1, 2, 5, 8]
   whereIn('course_id', [1, 2, 5, 8])
   ```

**View:** `resources/views/teacher/dashboard.blade.php`

```html
<x-teacher-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold mb-6">Teacher Dashboard</h2>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Total Courses -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-gray-600">Total Courses</div>
                        <div class="text-3xl font-bold">{{ $totalCourses }}</div>
                    </div>
                </div>

                <!-- Active Courses -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-gray-600">Active Courses</div>
                        <div class="text-3xl font-bold">{{ $activeCourses }}</div>
                    </div>
                </div>

                <!-- Total Students -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-gray-600">Total Students</div>
                        <div class="text-3xl font-bold">{{ $totalStudents }}</div>
                    </div>
                </div>
            </div>

            <!-- My Courses -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-4">My Courses</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="px-4 py-2 text-left">Course Code</th>
                                    <th class="px-4 py-2 text-left">Course Name</th>
                                    <th class="px-4 py-2 text-left">Department</th>
                                    <th class="px-4 py-2 text-left">Students</th>
                                    <th class="px-4 py-2 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($courses as $course)
                                <tr class="border-b">
                                    <td class="px-4 py-2">{{ $course->course_code }}</td>
                                    <td class="px-4 py-2">{{ $course->course_name }}</td>
                                    <td class="px-4 py-2">{{ $course->department->name }}</td>
                                    <td class="px-4 py-2">{{ $course->enrollments_count }}</td>
                                    <td class="px-4 py-2">
                                        <a href="{{ route('teacher.courses.show', $course) }}" 
                                           class="text-blue-600 hover:underline">View</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-teacher-layout>
```

**Create Layout:** `resources/views/components/teacher-layout.blade.php`

```html
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - Teacher Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <!-- Navigation -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-bold">Teacher Panel</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('teacher.dashboard') }}" class="hover:underline">Dashboard</a>
                    <a href="{{ route('teacher.courses.index') }}" class="hover:underline">Courses</a>
                    <a href="{{ route('teacher.profile.index') }}" class="hover:underline">Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="hover:underline">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        {{ $slot }}
    </main>
</body>
</html>
```

**Commit:**
```bash
git add app/Http/Controllers/Teacher/DashboardController.php
git add resources/views/teacher/
git add resources/views/components/teacher-layout.blade.php
git commit -m "feat: implement teacher dashboard with course statistics"
```

---

## **Step 11.4: Attendance Marking System** (90 mins)

**Controller:** `app/Http/Controllers/Teacher/AttendanceController.php`

**Code:**
```php
<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\{Course, Attendance, Student};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB};

class AttendanceController extends Controller
{
    public function index()
    {
        $teacher = Auth::user()->teacher;
        
        $courses = $teacher->courses()
            ->where('is_active', true)
            ->with('department')
            ->get();

        return view('teacher.attendance.index', compact('courses'));
    }

    public function mark($courseId)
    {
        $teacher = Auth::user()->teacher;
        
        $course = Course::where('id', $courseId)
            ->where('teacher_id', $teacher->id)
            ->with(['students.user', 'department'])
            ->firstOrFail();

        // Get today's attendance if already marked
        $existingAttendance = Attendance::where('course_id', $courseId)
            ->whereDate('date', today())
            ->get()
            ->keyBy('student_id');

        return view('teacher.attendance.mark', compact('course', 'existingAttendance'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*' => 'required|in:present,absent,late,excused',
        ]);

        $teacher = Auth::user()->teacher;

        // Verify teacher owns this course
        $course = Course::where('id', $validated['course_id'])
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        DB::beginTransaction();

        try {
            foreach ($validated['attendance'] as $studentId => $status) {
                Attendance::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'course_id' => $validated['course_id'],
                        'date' => $validated['date'],
                    ],
                    [
                        'status' => $status,
                        'notes' => $request->input("notes.$studentId"),
                    ]
                );
            }

            DB::commit();

            return redirect()->route('teacher.attendance.index')
                ->with('success', 'Attendance marked successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Failed to mark attendance: ' . $e->getMessage());
        }
    }

    public function report($courseId)
    {
        $teacher = Auth::user()->teacher;
        
        $course = Course::where('id', $courseId)
            ->where('teacher_id', $teacher->id)
            ->with(['students.user'])
            ->firstOrFail();

        // Get attendance statistics for each student
        $students = $course->students->map(function ($student) use ($courseId) {
            $totalClasses = Attendance::where('course_id', $courseId)
                ->where('student_id', $student->id)
                ->count();

            $presentClasses = Attendance::where('course_id', $courseId)
                ->where('student_id', $student->id)
                ->where('status', 'present')
                ->count();

            $percentage = $totalClasses > 0 
                ? round(($presentClasses / $totalClasses) * 100, 2) 
                : 0;

            return [
                'student' => $student,
                'total_classes' => $totalClasses,
                'present' => $presentClasses,
                'percentage' => $percentage,
            ];
        });

        return view('teacher.attendance.report', compact('course', 'students'));
    }
}
```

**Explanation:**

1. **`updateOrCreate()`**
   ```php
   // If attendance exists for this student + course + date: UPDATE
   // If not exists: CREATE
   Attendance::updateOrCreate(
       ['student_id' => 1, 'course_id' => 5, 'date' => '2025-10-10'],
       ['status' => 'present']
   );
   ```

2. **`keyBy('student_id')`**
   ```php
   // Convert collection to array with student_id as key
   // Before: [0 => Attendance, 1 => Attendance]
   // After: [5 => Attendance, 10 => Attendance]
   // Access: $existingAttendance[5]  // Attendance for student 5
   ```

**View:** `resources/views/teacher/attendance/mark.blade.php`

```html
<x-teacher-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold mb-6">
                Mark Attendance - {{ $course->course_name }}
            </h2>

            <form method="POST" action="{{ route('teacher.attendance.store') }}">
                @csrf

                <input type="hidden" name="course_id" value="{{ $course->id }}">

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Date</label>
                    <input type="date" 
                           name="date" 
                           value="{{ old('date', today()->toDateString()) }}"
                           class="border px-3 py-2 rounded"
                           required>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left">Roll</th>
                                <th class="px-6 py-3 text-left">Name</th>
                                <th class="px-6 py-3 text-left">Status</th>
                                <th class="px-6 py-3 text-left">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($course->students as $student)
                            <tr>
                                <td class="px-6 py-4">{{ $student->roll_number }}</td>
                                <td class="px-6 py-4">{{ $student->user->name }}</td>
                                <td class="px-6 py-4">
                                    <select name="attendance[{{ $student->id }}]" 
                                            class="border px-3 py-2 rounded" 
                                            required>
                                        @php
                                            $currentStatus = $existingAttendance[$student->id]->status ?? 'present';
                                        @endphp
                                        <option value="present" {{ $currentStatus == 'present' ? 'selected' : '' }}>Present</option>
                                        <option value="absent" {{ $currentStatus == 'absent' ? 'selected' : '' }}>Absent</option>
                                        <option value="late" {{ $currentStatus == 'late' ? 'selected' : '' }}>Late</option>
                                        <option value="excused" {{ $currentStatus == 'excused' ? 'selected' : '' }}>Excused</option>
                                    </select>
                                </td>
                                <td class="px-6 py-4">
                                    <input type="text" 
                                           name="notes[{{ $student->id }}]"
                                           value="{{ $existingAttendance[$student->id]->notes ?? '' }}"
                                           class="border px-3 py-2 rounded w-full"
                                           placeholder="Optional notes">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded">
                        Save Attendance
                    </button>
                    <a href="{{ route('teacher.attendance.index') }}" 
                       class="ml-4 text-gray-600 hover:underline">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-teacher-layout>
```

**Commit:**
```bash
git add app/Http/Controllers/Teacher/AttendanceController.php
git add resources/views/teacher/attendance/
git commit -m "feat: implement attendance marking system for teachers"
```

---

## **✅ Day 11 Checklist:**

- [x] Teacher controllers created
- [x] Teacher dashboard implemented
- [x] Course statistics calculated
- [x] Attendance marking system complete
- [x] Attendance reports functional
- [x] Teacher layout created
- [x] All code committed

**Status:** Teacher Module Part 1 complete! Ready for Day 12.

---

# 📅 **DAY 12: TEACHER MODULE - PART 2**

## **Goals:**
- ✅ Implement exam management
- ✅ Create marks entry system
- ✅ Add result publishing

## **Time Estimate:** 5-6 hours

---

## **Step 12.1: Create Exam Controller** (20 mins)

```bash
php artisan make:controller Teacher/ExamController --resource
php artisan make:controller Teacher/ResultController
```

---

## **Step 12.2: Exam Management** (90 mins)

**Controller:** `app/Http/Controllers/Teacher/ExamController.php`

**Code:**
```php
<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\{Course, Exam};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    public function index()
    {
        $teacher = Auth::user()->teacher;
        
        $exams = Exam::whereHas('course', function($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })
        ->with('course')
        ->orderBy('exam_date', 'desc')
        ->paginate(15);

        return view('teacher.exams.index', compact('exams'));
    }

    public function create()
    {
        $teacher = Auth::user()->teacher;
        
        $courses = $teacher->courses()
            ->where('is_active', true)
            ->get();

        return view('teacher.exams.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $teacher = Auth::user()->teacher;

        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'exam_name' => 'required|string|max:255',
            'exam_type' => 'required|in:mid-term,final,quiz,assignment',
            'exam_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'total_marks' => 'required|integer|min:1',
            'passing_marks' => 'nullable|integer|min:1|lte:total_marks',
            'room_number' => 'nullable|string|max:50',
            'instructions' => 'nullable|string',
        ]);

        // Verify teacher owns this course
        $course = Course::where('id', $validated['course_id'])
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        Exam::create($validated);

        return redirect()->route('teacher.exams.index')
            ->with('success', 'Exam created successfully.');
    }

    public function show($id)
    {
        $teacher = Auth::user()->teacher;
        
        $exam = Exam::whereHas('course', function($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })
        ->with(['course.students.user', 'results'])
        ->findOrFail($id);

        return view('teacher.exams.show', compact('exam'));
    }

    public function edit($id)
    {
        $teacher = Auth::user()->teacher;
        
        $exam = Exam::whereHas('course', function($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })
        ->findOrFail($id);

        $courses = $teacher->courses()->where('is_active', true)->get();

        return view('teacher.exams.edit', compact('exam', 'courses'));
    }

    public function update(Request $request, $id)
    {
        $teacher = Auth::user()->teacher;
        
        $exam = Exam::whereHas('course', function($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })
        ->findOrFail($id);

        $validated = $request->validate([
            'exam_name' => 'required|string|max:255',
            'exam_type' => 'required|in:mid-term,final,quiz,assignment',
            'exam_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'total_marks' => 'required|integer|min:1',
            'passing_marks' => 'nullable|integer|min:1|lte:total_marks',
            'room_number' => 'nullable|string|max:50',
            'instructions' => 'nullable|string',
        ]);

        $exam->update($validated);

        return redirect()->route('teacher.exams.index')
            ->with('success', 'Exam updated successfully.');
    }

    public function destroy($id)
    {
        $teacher = Auth::user()->teacher;
        
        $exam = Exam::whereHas('course', function($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })
        ->findOrFail($id);

        $exam->delete();

        return redirect()->route('teacher.exams.index')
            ->with('success', 'Exam deleted successfully.');
    }
}
```

**Key Security Check:**
```php
// This ensures teacher can only access their own courses' exams
$exam = Exam::whereHas('course', function($q) use ($teacher) {
    $q->where('teacher_id', $teacher->id);
})->findOrFail($id);

// Without this, any teacher could access any exam!
```

---

## **Step 12.3: Marks Entry System** (120 mins)

**Controller:** `app/Http/Controllers/Teacher/ResultController.php`

**Code:**
```php
<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\{Exam, Result};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB};

class ResultController extends Controller
{
    public function enterMarks($examId)
    {
        $teacher = Auth::user()->teacher;
        
        $exam = Exam::whereHas('course', function($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })
        ->with(['course.students.user', 'results'])
        ->findOrFail($examId);

        // Organize existing results by student_id
        $existingResults = $exam->results->keyBy('student_id');

        return view('teacher.results.enter-marks', compact('exam', 'existingResults'));
    }

    public function saveMarks(Request $request)
    {
        $teacher = Auth::user()->teacher;

        $validated = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'marks' => 'required|array',
            'marks.*' => 'required|numeric|min:0',
        ]);

        // Verify teacher owns this exam
        $exam = Exam::whereHas('course', function($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })
        ->findOrFail($validated['exam_id']);

        // Validate marks don't exceed total_marks
        foreach ($validated['marks'] as $marks) {
            if ($marks > $exam->total_marks) {
                return redirect()->back()
                    ->with('error', "Marks cannot exceed {$exam->total_marks}");
            }
        }

        DB::beginTransaction();

        try {
            foreach ($validated['marks'] as $studentId => $marks) {
                // Calculate percentage
                $percentage = ($marks / $exam->total_marks) * 100;

                // Calculate grade
                $grade = $this->calculateGrade($percentage);

                // Calculate grade point
                $gradePoint = $this->calculateGradePoint($grade);

                Result::updateOrCreate(
                    [
                        'exam_id' => $exam->id,
                        'student_id' => $studentId,
                    ],
                    [
                        'marks_obtained' => $marks,
                        'percentage' => $percentage,
                        'grade' => $grade,
                        'grade_point' => $gradePoint,
                        'remarks' => $request->input("remarks.$studentId"),
                        'is_published' => false,  // Not published yet
                    ]
                );
            }

            DB::commit();

            return redirect()->route('teacher.exams.show', $exam->id)
                ->with('success', 'Marks entered successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Failed to save marks: ' . $e->getMessage());
        }
    }

    public function publish($examId)
    {
        $teacher = Auth::user()->teacher;
        
        $exam = Exam::whereHas('course', function($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })
        ->findOrFail($examId);

        // Update all results for this exam
        Result::where('exam_id', $exam->id)
            ->update(['is_published' => true]);

        $exam->update(['is_published' => true]);

        return redirect()->route('teacher.exams.show', $exam->id)
            ->with('success', 'Results published successfully. Students can now view their marks.');
    }

    public function unpublish($examId)
    {
        $teacher = Auth::user()->teacher;
        
        $exam = Exam::whereHas('course', function($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })
        ->findOrFail($examId);

        Result::where('exam_id', $exam->id)
            ->update(['is_published' => false]);

        $exam->update(['is_published' => false]);

        return redirect()->route('teacher.exams.show', $exam->id)
            ->with('success', 'Results unpublished successfully.');
    }

    private function calculateGrade($percentage)
    {
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
        $gradePoints = [
            'A+' => 4.00, 'A' => 3.75, 'A-' => 3.50,
            'B+' => 3.25, 'B' => 3.00, 'B-' => 2.75,
            'C+' => 2.50, 'C' => 2.25, 'D' => 2.00,
            'F' => 0.00,
        ];

        return $gradePoints[$grade] ?? 0.00;
    }
}
```

**Grading System Explanation:**

| Percentage | Grade | Grade Point |
|------------|-------|-------------|
| 80-100% | A+ | 4.00 |
| 75-79% | A | 3.75 |
| 70-74% | A- | 3.50 |
| 65-69% | B+ | 3.25 |
| 60-64% | B | 3.00 |
| 55-59% | B- | 2.75 |
| 50-54% | C+ | 2.50 |
| 45-49% | C | 2.25 |
| 40-44% | D | 2.00 |
| 0-39% | F | 0.00 |

**View:** `resources/views/teacher/results/enter-marks.blade.php`

```html
<x-teacher-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold mb-6">
                Enter Marks - {{ $exam->exam_name }}
            </h2>

            <div class="bg-gray-100 p-4 rounded mb-6">
                <p><strong>Course:</strong> {{ $exam->course->course_name }}</p>
                <p><strong>Total Marks:</strong> {{ $exam->total_marks }}</p>
                <p><strong>Passing Marks:</strong> {{ $exam->passing_marks ?? 'N/A' }}</p>
            </div>

            <form method="POST" action="{{ route('teacher.results.save-marks') }}">
                @csrf
                <input type="hidden" name="exam_id" value="{{ $exam->id }}">

                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left">Roll</th>
                                <th class="px-6 py-3 text-left">Name</th>
                                <th class="px-6 py-3 text-left">Marks (out of {{ $exam->total_marks }})</th>
                                <th class="px-6 py-3 text-left">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($exam->course->students as $student)
                            @php
                                $result = $existingResults[$student->id] ?? null;
                            @endphp
                            <tr>
                                <td class="px-6 py-4">{{ $student->roll_number }}</td>
                                <td class="px-6 py-4">{{ $student->user->name }}</td>
                                <td class="px-6 py-4">
                                    <input type="number" 
                                           name="marks[{{ $student->id }}]"
                                           value="{{ $result?->marks_obtained ?? 0 }}"
                                           min="0"
                                           max="{{ $exam->total_marks }}"
                                           step="0.01"
                                           class="border px-3 py-2 rounded w-32"
                                           required>
                                </td>
                                <td class="px-6 py-4">
                                    <input type="text" 
                                           name="remarks[{{ $student->id }}]"
                                           value="{{ $result?->remarks }}"
                                           class="border px-3 py-2 rounded w-full"
                                           placeholder="Optional">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded">
                        Save Marks
                    </button>
                    <a href="{{ route('teacher.exams.show', $exam) }}" 
                       class="ml-4 text-gray-600 hover:underline">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-teacher-layout>
```

**Commit:**
```bash
git add app/Http/Controllers/Teacher/ExamController.php
git add app/Http/Controllers/Teacher/ResultController.php
git add resources/views/teacher/exams/
git add resources/views/teacher/results/
git commit -m "feat: implement exam management and marks entry system"
```

---

## **✅ Day 12 Checklist:**

- [x] Exam CRUD implemented
- [x] Marks entry system complete
- [x] Grade calculation working
- [x] Result publishing functional
- [x] Security checks in place
- [x] All views created
- [x] Code committed

**Status:** Teacher Module complete! Ready for Day 13.

---

**[Continue with Days 13-20...]**

Days remaining:
- Day 13: Staff Module
- Day 14: Department Head Features
- Day 15: Profile Picture Upload
- Day 16-17: UI/UX Improvements
- Day 18: Testing
- Day 19: Bug Fixes
- Day 20: Deployment

**Total: Complete development workflow from Day 1 to Day 20!**


