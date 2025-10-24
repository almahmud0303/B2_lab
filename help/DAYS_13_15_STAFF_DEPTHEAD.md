# 👥 Days 13-15: Staff, Department Head & Features

## 📋 **OVERVIEW**

Complete code for Staff Module, Department Head features, and UI/UX improvements.

---

# 📅 **DAY 13: STAFF MODULE**

## **Goals:**
- ✅ Create staff controllers
- ✅ Build staff dashboard
- ✅ Implement library management
- ✅ Create book issue system

## **Time Estimate:** 5-6 hours

---

## **STEP 53: Verify Git Status** (1 min)

```bash
# Make sure you're on main branch
git branch
# Should show: * main

# Check you're up to date
git status
```

**Note:** We work directly on main branch - no feature branches needed!

---

## **STEP 54: Create Staff Controllers** (15 mins)

```bash
php artisan make:controller Staff/DashboardController
php artisan make:controller Staff/ProfileController
php artisan make:controller Staff/LibraryController --resource
php artisan make:controller Staff/BookIssueController
php artisan make:controller Staff/StudentController
```

---

## **STEP 55: Staff Dashboard Controller** (30 mins)

**File:** `app/Http/Controllers/Staff/DashboardController.php`

```php
<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\{Book, BookIssue, Student};
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $staff = Auth::user()->staff;

        if (!$staff) {
            abort(404, 'Staff profile not found');
        }

        $stats = [
            'total_books' => Book::count(),
            'available_books' => Book::where('is_available', true)->sum('available_copies'),
            'issued_books' => BookIssue::where('status', 'issued')->count(),
            'overdue_books' => BookIssue::where('status', 'overdue')->count(),
            'total_students' => Student::count(),
        ];

        // Recent book issues
        $recentIssues = BookIssue::with(['book', 'student.user', 'issuedByUser'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Books running low
        $lowStockBooks = Book::where('available_copies', '<=', 2)
            ->where('available_copies', '>', 0)
            ->orderBy('available_copies', 'asc')
            ->take(5)
            ->get();

        return view('staff.dashboard', compact('staff', 'stats', 'recentIssues', 'lowStockBooks'));
    }
}
```

---

## **STEP 56: Library Controller** (60 mins)

**File:** `app/Http/Controllers/Staff/LibraryController.php`

```php
<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    public function index()
    {
        $books = Book::orderBy('title')
            ->paginate(20);

        return view('staff.library.index', compact('books'));
    }

    public function create()
    {
        $categories = ['Programming', 'Database', 'Networking', 'Mathematics', 'Physics', 'Electronics', 'General'];
        
        return view('staff.library.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'isbn' => 'required|string|unique:books,isbn',
            'author' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'category' => 'required|string',
            'description' => 'nullable|string',
            'total_copies' => 'required|integer|min:1',
            'shelf_location' => 'nullable|string|max:50',
        ]);

        // Available copies equals total copies initially
        $validated['available_copies'] = $validated['total_copies'];
        $validated['is_available'] = true;

        Book::create($validated);

        return redirect()->route('staff.library.index')
            ->with('success', 'Book added successfully.');
    }

    public function show(Book $library)
    {
        $library->load('bookIssues.student.user');

        return view('staff.library.show', compact('library'));
    }

    public function edit(Book $library)
    {
        $categories = ['Programming', 'Database', 'Networking', 'Mathematics', 'Physics', 'Electronics', 'General'];
        
        return view('staff.library.edit', compact('library', 'categories'));
    }

    public function update(Request $request, Book $library)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'isbn' => 'required|string|unique:books,isbn,' . $library->id,
            'author' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'category' => 'required|string',
            'description' => 'nullable|string',
            'total_copies' => 'required|integer|min:1',
            'shelf_location' => 'nullable|string|max:50',
        ]);

        // Update availability status
        $validated['is_available'] = $library->available_copies > 0;

        $library->update($validated);

        return redirect()->route('staff.library.index')
            ->with('success', 'Book updated successfully.');
    }

    public function destroy(Book $library)
    {
        // Check if book has active issues
        $activeIssues = $library->bookIssues()
            ->where('status', 'issued')
            ->count();

        if ($activeIssues > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete book with active issues.');
        }

        $library->delete();

        return redirect()->route('staff.library.index')
            ->with('success', 'Book deleted successfully.');
    }
}
```

---

## **STEP 57: Book Issue Controller** (90 mins)

**File:** `app/Http/Controllers/Staff/BookIssueController.php`

```php
<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\{Book, BookIssue, Student};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB};

class BookIssueController extends Controller
{
    public function index()
    {
        $bookIssues = BookIssue::with(['book', 'student.user', 'issuedByUser'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('staff.book-issues.index', compact('bookIssues'));
    }

    public function create()
    {
        $books = Book::where('is_available', true)
            ->where('available_copies', '>', 0)
            ->orderBy('title')
            ->get();

        $students = Student::with('user')
            ->where('is_active', true)
            ->get();

        return view('staff.book-issues.create', compact('books', 'students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'student_id' => 'required|exists:students,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after:issue_date',
            'notes' => 'nullable|string',
        ]);

        $book = Book::findOrFail($validated['book_id']);

        // Check if book is available
        if ($book->available_copies <= 0) {
            return redirect()->back()
                ->with('error', 'Book is not available.');
        }

        // Check if student already has this book
        $existingIssue = BookIssue::where('book_id', $book->id)
            ->where('student_id', $validated['student_id'])
            ->where('status', 'issued')
            ->exists();

        if ($existingIssue) {
            return redirect()->back()
                ->with('error', 'Student already has this book issued.');
        }

        DB::beginTransaction();

        try {
            // Create book issue
            BookIssue::create([
                'book_id' => $validated['book_id'],
                'student_id' => $validated['student_id'],
                'issued_by' => Auth::id(),
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'],
                'status' => 'issued',
                'notes' => $validated['notes'],
            ]);

            // Decrease available copies
            $book->decrement('available_copies');
            
            // Update availability status
            if ($book->available_copies == 0) {
                $book->update(['is_available' => false]);
            }

            DB::commit();

            return redirect()->route('staff.book-issues.index')
                ->with('success', 'Book issued successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Failed to issue book: ' . $e->getMessage());
        }
    }

    public function return($id)
    {
        $bookIssue = BookIssue::with('book')->findOrFail($id);

        if ($bookIssue->status !== 'issued') {
            return redirect()->back()
                ->with('error', 'Book is not currently issued.');
        }

        DB::beginTransaction();

        try {
            // Calculate fine if overdue
            $fineAmount = 0;
            if (now()->gt($bookIssue->due_date)) {
                $daysOverdue = now()->diffInDays($bookIssue->due_date);
                $fineAmount = $daysOverdue * 10; // 10 BDT per day
            }

            // Update book issue
            $bookIssue->update([
                'status' => 'returned',
                'return_date' => now(),
                'fine_amount' => $fineAmount,
            ]);

            // Increase available copies
            $bookIssue->book->increment('available_copies');
            $bookIssue->book->update(['is_available' => true]);

            DB::commit();

            $message = $fineAmount > 0 
                ? "Book returned successfully. Fine: BDT {$fineAmount}" 
                : "Book returned successfully.";

            return redirect()->route('staff.book-issues.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Failed to return book: ' . $e->getMessage());
        }
    }

    public function renew($id)
    {
        $bookIssue = BookIssue::findOrFail($id);

        if ($bookIssue->status !== 'issued') {
            return redirect()->back()
                ->with('error', 'Cannot renew: Book not currently issued.');
        }

        // Extend due date by 7 days
        $bookIssue->update([
            'due_date' => $bookIssue->due_date->addDays(7),
        ]);

        return redirect()->route('staff.book-issues.index')
            ->with('success', 'Book renewed successfully. New due date: ' . $bookIssue->due_date->format('Y-m-d'));
    }
}
```

---

## **STEP 58: Create Staff Layout** (30 mins)

**File:** `resources/views/components/staff-layout.blade.php`

```html
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - Staff Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-purple-600 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-bold text-white">{{ config('app.name') }} - Staff</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('staff.dashboard') }}" class="text-white hover:text-gray-200">Dashboard</a>
                    <a href="{{ route('staff.library.index') }}" class="text-white hover:text-gray-200">Library</a>
                    <a href="{{ route('staff.book-issues.index') }}" class="text-white hover:text-gray-200">Book Issues</a>
                    <a href="{{ route('staff.students.index') }}" class="text-white hover:text-gray-200">Students</a>
                    
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="text-white hover:text-gray-200 flex items-center">
                            {{ Auth::user()->name }}
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" 
                             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
                            <a href="{{ route('staff.profile.index') }}" 
                               class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Profile</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main>
        {{ $slot }}
    </main>
</body>
</html>
```

---

## **STEP 59: Create Staff Routes** (15 mins)

**File:** `routes/web.php`

Add after teacher routes:

```php
// Staff Routes
Route::middleware(['auth', 'role:staff', 'prevent-back'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Staff\DashboardController::class, 'index'])->name('dashboard');
    
    // Profile routes
    Route::get('/profile', [App\Http\Controllers\Staff\ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [App\Http\Controllers\Staff\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\Staff\ProfileController::class, 'update'])->name('profile.update');
    
    // Library management
    Route::resource('library', App\Http\Controllers\Staff\LibraryController::class);
    
    // Book issue management
    Route::get('/book-issues', [App\Http\Controllers\Staff\BookIssueController::class, 'index'])->name('book-issues.index');
    Route::get('/book-issues/create', [App\Http\Controllers\Staff\BookIssueController::class, 'create'])->name('book-issues.create');
    Route::post('/book-issues', [App\Http\Controllers\Staff\BookIssueController::class, 'store'])->name('book-issues.store');
    Route::post('/book-issues/{bookIssue}/return', [App\Http\Controllers\Staff\BookIssueController::class, 'return'])->name('book-issues.return');
    Route::post('/book-issues/{bookIssue}/renew', [App\Http\Controllers\Staff\BookIssueController::class, 'renew'])->name('book-issues.renew');
    
    // Student records (limited access)
    Route::get('/students', [App\Http\Controllers\Staff\StudentController::class, 'index'])->name('students.index');
    Route::get('/students/{student}', [App\Http\Controllers\Staff\StudentController::class, 'show'])->name('students.show');
});
```

---

## **STEP 60: Create Staff Seeder** (20 mins)

```bash
php artisan make:seeder StaffSeeder
```

**File:** `database/seeders/StaffSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\{User, Staff, Department};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        $departments = Department::all();

        $staffMembers = [
            [
                'name' => 'Md. Jamal Uddin',
                'email' => 'jamal@kuet.ac.bd',
                'position' => 'Senior Librarian',
                'location' => 'library',
                'department_code' => null,
            ],
            [
                'name' => 'Mrs. Nasrin Akter',
                'email' => 'nasrin@kuet.ac.bd',
                'position' => 'Assistant Librarian',
                'location' => 'library',
                'department_code' => null,
            ],
            [
                'name' => 'Md. Habib Rahman',
                'email' => 'habib@kuet.ac.bd',
                'position' => 'Administrative Officer',
                'location' => 'administration',
                'department_code' => null,
            ],
            [
                'name' => 'Mrs. Shapla Begum',
                'email' => 'shapla@kuet.ac.bd',
                'position' => 'Department Assistant',
                'location' => 'department',
                'department_code' => 'CSE',
            ],
        ];

        foreach ($staffMembers as $index => $staffData) {
            $department = null;
            if ($staffData['department_code']) {
                $department = Department::where('code', $staffData['department_code'])->first();
            }

            // Create user
            $user = User::create([
                'name' => $staffData['name'],
                'email' => $staffData['email'],
                'password' => Hash::make('password'),
                'role' => 'staff',
                'phone' => '01900' . str_pad($index + 100, 6, '0', STR_PAD_LEFT),
                'is_active' => true,
            ]);

            // Create staff profile
            Staff::create([
                'user_id' => $user->id,
                'department_id' => $department?->id,
                'employee_id' => 'S-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'position' => $staffData['position'],
                'qualification' => 'Bachelor Degree',
                'salary' => rand(30000, 60000),
                'joining_date' => now()->subYears(rand(1, 5)),
                'employment_type' => 'full-time',
                'location' => $staffData['location'],
                'responsibilities' => 'General duties',
                'is_active' => true,
            ]);

            $this->command->info("Created staff: {$staffData['name']} ({$staffData['email']})");
        }
    }
}
```

**Update DatabaseSeeder.php:**
```php
$this->call([
    DepartmentSeeder::class,
    HallSeeder::class,
    AdminSeeder::class,
    TeacherSeeder::class,
    CourseSeeder::class,
    StudentSeeder::class,
    StaffSeeder::class,  // Add this line
]);
```

---

## **STEP 61: Run Updated Seeders** (5 mins)

```bash
php artisan db:seed --class=StaffSeeder
```

---

## **STEP 62: Test Staff Login** (10 mins)

```bash
# Open: http://localhost:8000/login
# Email: jamal@kuet.ac.bd
# Password: password

# Should redirect to staff dashboard
```

---

## **STEP 63: Commit Day 13 Progress**

```bash
# Commit your work to main branch
git add .
git commit -m "feat: implement staff module with library and book issue management"
git push origin main
```

---

**✅ DAY 13 CHECKLIST:**
- [x] Staff controllers created
- [x] Staff dashboard implemented
- [x] Library management complete
- [x] Book issue/return system working
- [x] Staff seeder created
- [x] All code committed

---

# 🏛️ **DAY 14: DEPARTMENT HEAD MODULE**

## **Goals:**
- ✅ Add department head flag
- ✅ Create department head controllers
- ✅ Implement course assignment
- ✅ Add workload reports

## **Time Estimate:** 4-5 hours

---

## **STEP 64: Verify Git Status** (1 min)

```bash
# Make sure you're on main branch
git branch
# Should show: * main

# Check you're up to date
git status
```

**Note:** We work directly on main branch - no feature branches needed!

---

## **STEP 65: Add Department Head Flag** (15 mins)

```bash
php artisan make:migration add_is_department_head_to_teachers_table
```

**File:** `database/migrations/YYYY_MM_DD_add_is_department_head_to_teachers_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->boolean('is_department_head')->default(false)->after('specialization');
        });
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn('is_department_head');
        });
    }
};
```

```bash
# Run migration
php artisan migrate
```

---

## **STEP 66: Update Teacher Model** (15 mins)

**File:** `app/Models/Teacher.php`

Add these methods:

```php
// Add to $fillable array
protected $fillable = [
    'user_id',
    'department_id',
    'employee_id',
    'designation',
    'qualification',
    'salary',
    'joining_date',
    'employment_type',
    'specialization',
    'is_department_head',  // Add this line
    'is_active',
];

// Add to $casts array
protected $casts = [
    'joining_date' => 'date',
    'salary' => 'decimal:2',
    'is_department_head' => 'boolean',  // Add this line
    'is_active' => 'boolean',
];

// Add helper methods
public function isDepartmentHead()
{
    return $this->is_department_head || 
           Department::where('head_user_id', $this->user_id)->exists();
}

public function getManagedDepartment()
{
    return Department::where('head_user_id', $this->user_id)->first();
}
```

---

## **STEP 67: Create Department Head Controllers** (30 mins)

```bash
php artisan make:controller DepartmentHead/DashboardController
php artisan make:controller DepartmentHead/CourseAssignmentController
```

**File:** `app/Http/Controllers/DepartmentHead/DashboardController.php`

```php
<?php

namespace App\Http\Controllers\DepartmentHead;

use App\Http\Controllers\Controller;
use App\Models\{Department, Teacher, Course, Student};
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $teacher = Auth::user()->teacher;

        if (!$teacher || !$teacher->isDepartmentHead()) {
            abort(403, 'Access denied. Department head privileges required.');
        }

        // Get managed department
        $department = $teacher->getManagedDepartment();

        if (!$department) {
            abort(404, 'No department assigned as head.');
        }

        // Department statistics
        $stats = [
            'total_teachers' => $department->teachers()->count(),
            'active_teachers' => $department->teachers()->where('is_active', true)->count(),
            'total_students' => $department->students()->count(),
            'active_students' => $department->students()->where('is_active', true)->count(),
            'total_courses' => $department->courses()->count(),
            'active_courses' => $department->courses()->where('is_active', true)->count(),
        ];

        // Teachers in department
        $teachers = $department->teachers()
            ->with('user')
            ->withCount('courses')
            ->where('is_active', true)
            ->get();

        // Courses in department
        $courses = $department->courses()
            ->with(['teacher.user'])
            ->where('is_active', true)
            ->get();

        // Recent students
        $recentStudents = $department->students()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('department-head.dashboard', compact(
            'teacher',
            'department',
            'stats',
            'teachers',
            'courses',
            'recentStudents'
        ));
    }
}
```

---

## **STEP 68: Course Assignment Controller** (60 mins)

**File:** `app/Http/Controllers/DepartmentHead/CourseAssignmentController.php`

```php
<?php

namespace App\Http\Controllers\DepartmentHead;

use App\Http\Controllers\Controller;
use App\Models\{Course, Teacher};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseAssignmentController extends Controller
{
    private function getDepartment()
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher || !$teacher->isDepartmentHead()) {
            abort(403, 'Access denied');
        }

        $department = $teacher->getManagedDepartment();

        if (!$department) {
            abort(404, 'No department assigned');
        }

        return $department;
    }

    public function index()
    {
        $department = $this->getDepartment();

        $courses = Course::where('department_id', $department->id)
            ->with(['teacher.user'])
            ->orderBy('course_code')
            ->paginate(20);

        return view('department-head.course-assignment.index', compact('courses', 'department'));
    }

    public function assign($courseId)
    {
        $department = $this->getDepartment();

        $course = Course::where('id', $courseId)
            ->where('department_id', $department->id)
            ->firstOrFail();

        $teachers = Teacher::where('department_id', $department->id)
            ->where('is_active', true)
            ->with('user')
            ->withCount('courses')
            ->get();

        return view('department-head.course-assignment.assign', compact('course', 'teachers', 'department'));
    }

    public function updateAssignment(Request $request, $courseId)
    {
        $department = $this->getDepartment();

        $validated = $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
        ]);

        $course = Course::where('id', $courseId)
            ->where('department_id', $department->id)
            ->firstOrFail();

        $teacher = Teacher::where('id', $validated['teacher_id'])
            ->where('department_id', $department->id)
            ->firstOrFail();

        $course->update(['teacher_id' => $teacher->id]);

        return redirect()->route('department-head.course-assignment.index')
            ->with('success', "Course assigned to {$teacher->user->name} successfully.");
    }

    public function unassign($courseId)
    {
        $department = $this->getDepartment();

        $course = Course::where('id', $courseId)
            ->where('department_id', $department->id)
            ->firstOrFail();

        $course->update(['teacher_id' => null]);

        return redirect()->route('department-head.course-assignment.index')
            ->with('success', 'Teacher unassigned from course.');
    }

    public function workloadReport()
    {
        $department = $this->getDepartment();

        $teachers = Teacher::where('department_id', $department->id)
            ->where('is_active', true)
            ->with(['user', 'courses'])
            ->withCount('courses')
            ->get()
            ->map(function ($teacher) {
                $totalCredits = $teacher->courses->sum('credit_hours');
                $totalStudents = $teacher->courses->sum(function ($course) {
                    return $course->enrollments()->where('status', 'enrolled')->count();
                });

                return [
                    'teacher' => $teacher,
                    'total_courses' => $teacher->courses_count,
                    'total_credits' => $totalCredits,
                    'total_students' => $totalStudents,
                ];
            })
            ->sortByDesc('total_credits');

        return view('department-head.course-assignment.workload', compact('teachers', 'department'));
    }
}
```

---

## **STEP 69: Create Department Head Routes** (15 mins)

**File:** `routes/web.php`

Add after staff routes:

```php
// Department Head Routes
Route::middleware(['auth', 'prevent-back'])->prefix('department-head')->name('department-head.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DepartmentHead\DashboardController::class, 'index'])->name('dashboard');
    
    // Course assignment
    Route::get('/course-assignment', [App\Http\Controllers\DepartmentHead\CourseAssignmentController::class, 'index'])->name('course-assignment.index');
    Route::get('/course-assignment/{course}/assign', [App\Http\Controllers\DepartmentHead\CourseAssignmentController::class, 'assign'])->name('course-assignment.assign');
    Route::put('/course-assignment/{course}', [App\Http\Controllers\DepartmentHead\CourseAssignmentController::class, 'updateAssignment'])->name('course-assignment.update');
    Route::delete('/course-assignment/{course}/unassign', [App\Http\Controllers\DepartmentHead\CourseAssignmentController::class, 'unassign'])->name('course-assignment.unassign');
    Route::get('/course-assignment/workload-report', [App\Http\Controllers\DepartmentHead\CourseAssignmentController::class, 'workloadReport'])->name('course-assignment.workload');
});
```

---

## **STEP 70: Update Teacher Layout** (20 mins)

**File:** `resources/views/components/teacher-layout.blade.php`

Add department head menu section:

```html
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - Teacher Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-green-600 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-bold text-white">{{ config('app.name') }} - Teacher</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('teacher.dashboard') }}" class="text-white hover:text-gray-200">Dashboard</a>
                    <a href="{{ route('teacher.courses.index') }}" class="text-white hover:text-gray-200">My Courses</a>
                    <a href="{{ route('teacher.attendance.index') }}" class="text-white hover:text-gray-200">Attendance</a>
                    <a href="{{ route('teacher.exams.index') }}" class="text-white hover:text-gray-200">Exams</a>
                    
                    <!-- Department Head Menu (conditional) -->
                    @if(Auth::user()->teacher && Auth::user()->teacher->isDepartmentHead())
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="text-white hover:text-gray-200 flex items-center">
                            Dept Head
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" 
                             class="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg py-1 z-10">
                            <a href="{{ route('department-head.dashboard') }}" 
                               class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Department Dashboard</a>
                            <a href="{{ route('department-head.course-assignment.index') }}" 
                               class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Course Assignment</a>
                            <a href="{{ route('department-head.course-assignment.workload') }}" 
                               class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Workload Report</a>
                        </div>
                    </div>
                    @endif
                    
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="text-white hover:text-gray-200 flex items-center">
                            {{ Auth::user()->name }}
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" 
                             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
                            <a href="{{ route('teacher.profile.index') }}" 
                               class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Profile</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        </div>
    @endif

    <main>
        {{ $slot }}
    </main>
</body>
</html>
```

---

## **STEP 71: Update Dashboard Route** (10 mins)

**File:** `routes/web.php`

Update main dashboard route:

```php
Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->isTeacher() || $user->isDepartmentHead()) {
        // Check if teacher is also department head
        if ($user->teacher && $user->teacher->isDepartmentHead()) {
            return redirect()->route('department-head.dashboard');
        }
        return redirect()->route('teacher.dashboard');
    } elseif ($user->isStudent()) {
        return redirect()->route('student.dashboard');
    } elseif ($user->isStaff()) {
        return redirect()->route('staff.dashboard');
    }

    abort(403, 'Unauthorized access');
})->middleware(['auth', 'verified', 'prevent-back'])->name('dashboard');
```

---

## **STEP 72: Test Department Head** (15 mins)

```bash
# Login as department head
# Email: karim@kuet.ac.bd (CSE Head)
# Password: password

# Should see:
# ✅ Department Head dashboard
# ✅ "Dept Head" menu in navigation
# ✅ Department statistics
```

---

## **STEP 73: Commit Day 14 Progress**

```bash
git add .
git commit -m "feat: implement department head module with course assignment"
git push -u origin feature/department-head

# Create pull request and merge
git checkout develop
git pull origin develop
git branch -d feature/department-head
```

---

**✅ DAY 14 CHECKLIST:**
- [x] Department head flag added
- [x] Department head controllers created
- [x] Course assignment implemented
- [x] Workload reports functional
- [x] Teacher layout updated
- [x] Dashboard route updated
- [x] All code committed

---

# 🎨 **DAY 15: UI/UX IMPROVEMENTS**

## **Goals:**
- ✅ Enhance all layouts
- ✅ Add consistent styling
- ✅ Improve navigation
- ✅ Add responsive design

## **Time Estimate:** 4-5 hours

---

## **STEP 74: Enhance All Layouts** (120 mins)

Update each layout file (admin, student, teacher, staff) with:
- Consistent color schemes
- Better navigation
- Improved mobile responsiveness
- Loading states
- Better error/success messages

---

## **STEP 75: Add Search Functionality** (60 mins)

Example for student search in admin panel:

**Controller:**
```php
public function index(Request $request)
{
    $query = Student::with('user', 'department');

    // Search
    if ($request->filled('search')) {
        $search = $request->search;
        $query->whereHas('user', function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        })->orWhere('student_id', 'like', "%{$search}%");
    }

    // Filter by department
    if ($request->filled('department')) {
        $query->where('department_id', $request->department);
    }

    // Filter by status
    if ($request->filled('status')) {
        $query->where('is_active', $request->status === 'active');
    }

    $students = $query->paginate(20);

    $departments = Department::orderBy('name')->get();

    return view('admin.students.index', compact('students', 'departments'));
}
```

---

## **STEP 76: Commit UI Improvements**

```bash
git checkout develop
git pull origin develop
git checkout -b feature/ui-improvements

git add .
git commit -m "feat: enhance UI/UX with search, filters, and responsive design"
git push -u origin feature/ui-improvements
```

---

**Continue to DAYS_16_20_FINAL.md for final days...**

