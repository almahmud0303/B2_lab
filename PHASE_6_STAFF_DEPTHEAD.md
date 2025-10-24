# Phase 6: Staff & Department Head Module (Days 21-24)

## 🎯 **Phase 6 Objectives**
- Create staff dashboard with library management
- Implement book issue/return system
- Build department head dashboard
- Create course assignment management
- Implement notice management for department heads
- Build faculty oversight system
- Create library statistics and reports

---

## 📅 **Day 21: Staff Module & Library Management**

### **Step 1: Create Additional Models**

```bash
php artisan make:model Book -m
php artisan make:model BookIssue -m
```

**File: `database/migrations/xxxx_xx_xx_create_books_table.php`**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('isbn')->unique();
            $table->string('author');
            $table->string('publisher')->nullable();
            $table->integer('publication_year')->nullable();
            $table->string('category')->nullable();
            $table->integer('total_copies')->default(1);
            $table->integer('available_copies')->default(1);
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['isbn', 'is_active']);
            $table->index(['category', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
```

**File: `database/migrations/xxxx_xx_xx_create_book_issues_table.php`**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('staff_id')->constrained()->onDelete('cascade');
            $table->date('issue_date');
            $table->date('due_date');
            $table->date('return_date')->nullable();
            $table->enum('status', ['issued', 'returned', 'overdue', 'lost'])->default('issued');
            $table->decimal('fine_amount', 8, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['student_id', 'status']);
            $table->index(['book_id', 'status']);
            $table->index(['staff_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_issues');
    }
};
```

### **Step 2: Book and BookIssue Models**

**File: `app/Models/Book.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'isbn',
        'author',
        'publisher',
        'publication_year',
        'category',
        'total_copies',
        'available_copies',
        'description',
        'location',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function bookIssues()
    {
        return $this->hasMany(BookIssue::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('available_copies', '>', 0);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Helper methods
    public function getIsAvailableAttribute()
    {
        return $this->available_copies > 0;
    }

    public function getBorrowedCountAttribute()
    {
        return $this->total_copies - $this->available_copies;
    }
}
```

**File: `app/Models/BookIssue.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookIssue extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'book_id',
        'student_id',
        'staff_id',
        'issue_date',
        'due_date',
        'return_date',
        'status',
        'fine_amount',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'due_date' => 'date',
            'return_date' => 'date',
            'fine_amount' => 'decimal:2',
        ];
    }

    // Relationships
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    // Scopes
    public function scopeIssued($query)
    {
        return $query->where('status', 'issued');
    }

    public function scopeReturned($query)
    {
        return $query->where('status', 'returned');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    // Helper methods
    public function getIsOverdueAttribute()
    {
        return $this->due_date < now() && $this->status === 'issued';
    }

    public function getDaysOverdueAttribute()
    {
        if ($this->is_overdue) {
            return now()->diffInDays($this->due_date);
        }
        return 0;
    }

    public function getStudentNameAttribute()
    {
        return $this->student->user->name;
    }

    public function getBookTitleAttribute()
    {
        return $this->book->title;
    }
}
```

### **Step 3: Staff Dashboard Controller**

**File: `app/Http/Controllers/Staff/DashboardController.php`**

```php
<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookIssue;
use App\Models\Student;
use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $staff = Auth::user()->staff;
        
        if (!$staff) {
            abort(404, 'Staff profile not found');
        }

        // Get library statistics
        $stats = [
            'total_books' => Book::count(),
            'available_books' => Book::where('available_copies', '>', 0)->count(),
            'total_issues' => BookIssue::count(),
            'active_issues' => BookIssue::where('status', 'issued')->count(),
            'overdue_issues' => BookIssue::where('status', 'overdue')->count(),
            'total_students' => Student::count(),
        ];

        // Get recent book issues
        $recentIssues = BookIssue::with(['book', 'student.user'])
            ->latest()
            ->limit(10)
            ->get();

        // Get overdue books
        $overdueBooks = BookIssue::with(['book', 'student.user'])
            ->where('status', 'overdue')
            ->orWhere(function($query) {
                $query->where('status', 'issued')
                      ->where('due_date', '<', now());
            })
            ->latest()
            ->limit(10)
            ->get();

        // Get recent notices
        $recentNotices = Notice::where('is_published', true)
            ->where(function($query) {
                $query->whereJsonContains('target_roles', 'staff')
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

        return view('staff.dashboard', compact(
            'staff',
            'stats',
            'recentIssues',
            'overdueBooks',
            'recentNotices'
        ));
    }
}
```

### **Step 4: Book Management Controller**

**File: `app/Http/Controllers/Staff/BookController.php`**

```php
<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by availability
        if ($request->filled('availability')) {
            if ($request->availability === 'available') {
                $query->where('available_copies', '>', 0);
            } elseif ($request->availability === 'unavailable') {
                $query->where('available_copies', 0);
            }
        }

        $books = $query->latest()->paginate(15);

        $categories = Book::distinct()->pluck('category')->filter();

        return view('staff.books.index', compact('books', 'categories'));
    }

    public function create()
    {
        return view('staff.books.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'isbn' => 'required|string|max:255|unique:books',
            'author' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|min:1800|max:' . date('Y'),
            'category' => 'nullable|string|max:255',
            'total_copies' => 'required|integer|min:1|max:1000',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        Book::create([
            'title' => $request->title,
            'isbn' => $request->isbn,
            'author' => $request->author,
            'publisher' => $request->publisher,
            'publication_year' => $request->publication_year,
            'category' => $request->category,
            'total_copies' => $request->total_copies,
            'available_copies' => $request->total_copies,
            'description' => $request->description,
            'location' => $request->location,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('staff.books.index')
            ->with('success', 'Book added successfully.');
    }

    public function show(Book $book)
    {
        $book->load(['bookIssues.student.user', 'bookIssues.staff.user']);
        
        $stats = [
            'total_issues' => $book->bookIssues()->count(),
            'active_issues' => $book->bookIssues()->where('status', 'issued')->count(),
            'returned_books' => $book->bookIssues()->where('status', 'returned')->count(),
            'overdue_books' => $book->bookIssues()->where('status', 'overdue')->count(),
        ];

        $recentIssues = $book->bookIssues()
            ->with(['student.user', 'staff.user'])
            ->latest()
            ->limit(10)
            ->get();

        return view('staff.books.show', compact('book', 'stats', 'recentIssues'));
    }

    public function edit(Book $book)
    {
        return view('staff.books.edit', compact('book'));
    }

    public function update(Request $request, Book $book)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'isbn' => 'required|string|max:255|unique:books,isbn,' . $book->id,
            'author' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|min:1800|max:' . date('Y'),
            'category' => 'nullable|string|max:255',
            'total_copies' => 'required|integer|min:1|max:1000',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        // Calculate new available copies
        $borrowedCount = $book->total_copies - $book->available_copies;
        $newAvailableCopies = max(0, $request->total_copies - $borrowedCount);

        $book->update([
            'title' => $request->title,
            'isbn' => $request->isbn,
            'author' => $request->author,
            'publisher' => $request->publisher,
            'publication_year' => $request->publication_year,
            'category' => $request->category,
            'total_copies' => $request->total_copies,
            'available_copies' => $newAvailableCopies,
            'description' => $request->description,
            'location' => $request->location,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('staff.books.index')
            ->with('success', 'Book updated successfully.');
    }

    public function destroy(Book $book)
    {
        $book->delete();
        return redirect()->route('staff.books.index')
            ->with('success', 'Book deleted successfully.');
    }
}
```

---

## 📅 **Day 22: Book Issue Management**

### **Step 1: Book Issue Controller**

**File: `app/Http/Controllers/Staff/BookIssueController.php`**

```php
<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\BookIssue;
use App\Models\Book;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookIssueController extends Controller
{
    public function index(Request $request)
    {
        $query = BookIssue::with(['book', 'student.user', 'staff.user']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by student
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        // Filter by book
        if ($request->filled('book_id')) {
            $query->where('book_id', $request->book_id);
        }

        $bookIssues = $query->latest()->paginate(20);

        $books = Book::where('available_copies', '>', 0)->get();
        $students = Student::with('user')->get();

        return view('staff.book-issues.index', compact('bookIssues', 'books', 'students'));
    }

    public function create()
    {
        $books = Book::where('available_copies', '>', 0)->get();
        $students = Student::with('user')->get();
        
        return view('staff.book-issues.create', compact('books', 'students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'student_id' => 'required|exists:students,id',
            'due_date' => 'required|date|after:today',
            'notes' => 'nullable|string',
        ]);

        $book = Book::find($request->book_id);
        $student = Student::find($request->student_id);

        // Check if book is available
        if ($book->available_copies <= 0) {
            return redirect()->back()
                ->with('error', 'Book is not available.');
        }

        // Check if student has overdue books
        $overdueCount = BookIssue::where('student_id', $student->id)
            ->where('status', 'issued')
            ->where('due_date', '<', now())
            ->count();

        if ($overdueCount > 0) {
            return redirect()->back()
                ->with('error', 'Student has overdue books. Cannot issue new books.');
        }

        // Check if student already has this book
        $existingIssue = BookIssue::where('student_id', $student->id)
            ->where('book_id', $book->id)
            ->where('status', 'issued')
            ->first();

        if ($existingIssue) {
            return redirect()->back()
                ->with('error', 'Student already has this book.');
        }

        // Create book issue
        $bookIssue = BookIssue::create([
            'book_id' => $request->book_id,
            'student_id' => $request->student_id,
            'staff_id' => Auth::user()->staff->id,
            'issue_date' => now(),
            'due_date' => $request->due_date,
            'status' => 'issued',
            'notes' => $request->notes,
        ]);

        // Update book available copies
        $book->decrement('available_copies');

        return redirect()->route('staff.book-issues.index')
            ->with('success', 'Book issued successfully.');
    }

    public function show(BookIssue $bookIssue)
    {
        $bookIssue->load(['book', 'student.user', 'staff.user']);
        return view('staff.book-issues.show', compact('bookIssue'));
    }

    public function return(BookIssue $bookIssue)
    {
        // Check if book is already returned
        if ($bookIssue->status === 'returned') {
            return redirect()->back()
                ->with('error', 'Book is already returned.');
        }

        // Calculate fine if overdue
        $fineAmount = 0;
        if ($bookIssue->due_date < now()) {
            $daysOverdue = now()->diffInDays($bookIssue->due_date);
            $fineAmount = $daysOverdue * 10; // 10 TK per day
        }

        // Update book issue
        $bookIssue->update([
            'return_date' => now(),
            'status' => 'returned',
            'fine_amount' => $fineAmount,
        ]);

        // Update book available copies
        $bookIssue->book->increment('available_copies');

        return redirect()->back()
            ->with('success', 'Book returned successfully.');
    }

    public function markOverdue(BookIssue $bookIssue)
    {
        if ($bookIssue->status !== 'issued') {
            return redirect()->back()
                ->with('error', 'Only issued books can be marked as overdue.');
        }

        $bookIssue->update(['status' => 'overdue']);

        return redirect()->back()
            ->with('success', 'Book marked as overdue.');
    }

    public function markLost(BookIssue $bookIssue)
    {
        if ($bookIssue->status === 'returned') {
            return redirect()->back()
                ->with('error', 'Book is already returned.');
        }

        $bookIssue->update(['status' => 'lost']);

        return redirect()->back()
            ->with('success', 'Book marked as lost.');
    }

    public function updateFine(Request $request, BookIssue $bookIssue)
    {
        $request->validate([
            'fine_amount' => 'required|numeric|min:0',
        ]);

        $bookIssue->update(['fine_amount' => $request->fine_amount]);

        return redirect()->back()
            ->with('success', 'Fine amount updated successfully.');
    }
}
```

### **Step 2: Staff Dashboard View**

**File: `resources/views/staff/dashboard.blade.php`**

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Staff Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-2">Welcome, {{ Auth::user()->name }}!</h1>
                    <p class="text-gray-600">{{ $staff->designation }} • {{ $staff->employee_id }}</p>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-blue-100 text-sm">Total Books</p>
                            <p class="text-3xl font-bold mt-2">{{ $stats['total_books'] }}</p>
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
                            <p class="text-green-100 text-sm">Available Books</p>
                            <p class="text-3xl font-bold mt-2">{{ $stats['available_books'] }}</p>
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
                            <p class="text-purple-100 text-sm">Active Issues</p>
                            <p class="text-3xl font-bold mt-2">{{ $stats['active_issues'] }}</p>
                        </div>
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-orange-100 text-sm">Overdue Books</p>
                            <p class="text-3xl font-bold mt-2">{{ $stats['overdue_issues'] }}</p>
                        </div>
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-orange-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
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
                            <a href="{{ route('staff.books.create') }}" 
                               class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                                <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-700">Add Book</span>
                            </a>
                            
                            <a href="{{ route('staff.book-issues.create') }}" 
                               class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                                <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-700">Issue Book</span>
                            </a>
                            
                            <a href="{{ route('staff.book-issues.index') }}" 
                               class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                                <svg class="w-6 h-6 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-700">Manage Issues</span>
                            </a>
                            
                            <a href="{{ route('staff.books.index') }}" 
                               class="flex items-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                                <svg class="w-6 h-6 text-orange-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-700">View Books</span>
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

            <!-- Recent Issues and Overdue Books -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-800">Recent Book Issues</h2>
                        <a href="{{ route('staff.book-issues.index') }}" class="text-blue-600 text-sm">View All</a>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($recentIssues as $issue)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex-1">
                                        <h3 class="font-medium text-gray-900">{{ $issue->book->title }}</h3>
                                        <p class="text-sm text-gray-600">{{ $issue->student->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $issue->issue_date->format('M d, Y') }}</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $issue->status === 'issued' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($issue->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-800">Overdue Books</h2>
                        <a href="{{ route('staff.book-issues.index', ['status' => 'overdue']) }}" class="text-blue-600 text-sm">View All</a>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($overdueBooks as $issue)
                                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                    <div class="flex-1">
                                        <h3 class="font-medium text-gray-900">{{ $issue->book->title }}</h3>
                                        <p class="text-sm text-gray-600">{{ $issue->student->user->name }}</p>
                                        <p class="text-xs text-red-600">Due: {{ $issue->due_date->format('M d, Y') }}</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            Overdue
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
```

---

## 📅 **Day 23: Department Head Module**

### **Step 1: Department Head Dashboard Controller**

**File: `app/Http/Controllers/DepartmentHead/DashboardController.php`**

```php
<?php

namespace App\Http\Controllers\DepartmentHead;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Course;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $teacher = $user->teacher;
        
        if (!$teacher || !$teacher->is_department_head) {
            abort(403, 'You are not authorized as a department head.');
        }

        $department = $teacher->department;
        
        if (!$department) {
            abort(404, 'Department not found for this department head.');
        }

        // Get department statistics
        $stats = [
            'total_teachers' => Teacher::where('department_id', $department->id)
                ->where('is_active', true)
                ->count(),
            'total_students' => Student::where('department_id', $department->id)
                ->where('is_active', true)
                ->count(),
            'total_courses' => Course::where('department_id', $department->id)
                ->where('is_active', true)
                ->count(),
            'active_courses' => Course::where('department_id', $department->id)
                ->where('is_active', true)
                ->whereHas('enrollments', function($q) {
                    $q->where('status', 'enrolled');
                })
                ->count(),
        ];

        // Get department teachers
        $teachers = Teacher::with('user')
            ->where('department_id', $department->id)
            ->where('is_active', true)
            ->get();

        // Get department courses
        $courses = Course::with('teacher.user', 'enrollments')
            ->where('department_id', $department->id)
            ->where('is_active', true)
            ->withCount(['enrollments' => function($q) {
                $q->where('status', 'enrolled');
            }])
            ->orderBy('academic_year')
            ->orderBy('semester')
            ->get();

        // Get recent students
        $recentStudents = Student::with('user')
            ->where('department_id', $department->id)
            ->where('is_active', true)
            ->latest()
            ->limit(10)
            ->get();

        return view('department-head.dashboard', compact(
            'department',
            'stats',
            'teachers',
            'courses',
            'recentStudents',
            'teacher'
        ));
    }
}
```

### **Step 2: Course Assignment Controller**

**File: `app/Http/Controllers/DepartmentHead/CourseAssignmentController.php`**

```php
<?php

namespace App\Http\Controllers\DepartmentHead;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseAssignmentController extends Controller
{
    public function index()
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher || !$teacher->is_department_head) {
            abort(403, 'You are not authorized as a department head.');
        }

        $department = $teacher->department;
        
        $courses = Course::where('department_id', $department->id)
            ->with(['teacher.user', 'enrollments'])
            ->withCount(['enrollments' => function($q) {
                $q->where('status', 'enrolled');
            }])
            ->latest()
            ->paginate(15);

        $teachers = Teacher::where('department_id', $department->id)
            ->where('is_active', true)
            ->with('user')
            ->get();

        return view('department-head.course-assignment.index', compact('courses', 'teachers'));
    }

    public function assign(Course $course)
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher || !$teacher->is_department_head) {
            abort(403, 'You are not authorized as a department head.');
        }

        // Ensure course belongs to department head's department
        if ($course->department_id !== $teacher->department_id) {
            abort(403, 'Unauthorized access to course.');
        }

        $teachers = Teacher::where('department_id', $teacher->department_id)
            ->where('is_active', true)
            ->with('user')
            ->get();

        return view('department-head.course-assignment.assign', compact('course', 'teachers'));
    }

    public function updateAssignment(Request $request, Course $course)
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher || !$teacher->is_department_head) {
            abort(403, 'You are not authorized as a department head.');
        }

        // Ensure course belongs to department head's department
        if ($course->department_id !== $teacher->department_id) {
            abort(403, 'Unauthorized access to course.');
        }

        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
        ]);

        $course->update(['teacher_id' => $request->teacher_id]);

        return redirect()->route('department-head.course-assignment.index')
            ->with('success', 'Course assignment updated successfully.');
    }

    public function unassign(Course $course)
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher || !$teacher->is_department_head) {
            abort(403, 'You are not authorized as a department head.');
        }

        // Ensure course belongs to department head's department
        if ($course->department_id !== $teacher->department_id) {
            abort(403, 'Unauthorized access to course.');
        }

        $course->update(['teacher_id' => null]);

        return redirect()->route('department-head.course-assignment.index')
            ->with('success', 'Course unassigned successfully.');
    }

    public function bulkAssign(Request $request)
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher || !$teacher->is_department_head) {
            abort(403, 'You are not authorized as a department head.');
        }

        $request->validate([
            'assignments' => 'required|array',
            'assignments.*.course_id' => 'required|exists:courses,id',
            'assignments.*.teacher_id' => 'required|exists:teachers,id',
        ]);

        foreach ($request->assignments as $assignment) {
            $course = Course::find($assignment['course_id']);
            
            // Ensure course belongs to department head's department
            if ($course->department_id === $teacher->department_id) {
                $course->update(['teacher_id' => $assignment['teacher_id']]);
            }
        }

        return redirect()->route('department-head.course-assignment.index')
            ->with('success', 'Bulk assignment completed successfully.');
    }

    public function workloadReport()
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher || !$teacher->is_department_head) {
            abort(403, 'You are not authorized as a department head.');
        }

        $department = $teacher->department;
        
        $teachers = Teacher::where('department_id', $department->id)
            ->where('is_active', true)
            ->with(['user', 'courses'])
            ->get()
            ->map(function($teacher) {
                return [
                    'teacher' => $teacher,
                    'total_courses' => $teacher->courses()->count(),
                    'active_courses' => $teacher->courses()->where('is_active', true)->count(),
                    'total_students' => $teacher->courses()
                        ->withCount(['enrollments' => function($q) {
                            $q->where('status', 'enrolled');
                        }])
                        ->get()
                        ->sum('enrollments_count'),
                ];
            });

        return view('department-head.course-assignment.workload-report', compact('teachers'));
    }
}
```

---

## 📅 **Day 24: Staff & Department Head Routes**

### **Step 1: Staff Routes**

**File: `routes/web.php` (Staff section)**

```php
// Staff Routes
Route::prefix('staff')->name('staff.')->middleware(['auth', 'role:staff', 'prevent.back'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Staff\DashboardController::class, 'index'])->name('dashboard');
    
    // Book Management
    Route::prefix('books')->group(function () {
        Route::get('/', [App\Http\Controllers\Staff\BookController::class, 'index'])->name('books.index');
        Route::get('/create', [App\Http\Controllers\Staff\BookController::class, 'create'])->name('books.create');
        Route::post('/', [App\Http\Controllers\Staff\BookController::class, 'store'])->name('books.store');
        Route::get('/{book}', [App\Http\Controllers\Staff\BookController::class, 'show'])->name('books.show');
        Route::get('/{book}/edit', [App\Http\Controllers\Staff\BookController::class, 'edit'])->name('books.edit');
        Route::put('/{book}', [App\Http\Controllers\Staff\BookController::class, 'update'])->name('books.update');
        Route::delete('/{book}', [App\Http\Controllers\Staff\BookController::class, 'destroy'])->name('books.destroy');
    });
    
    // Book Issue Management
    Route::prefix('book-issues')->group(function () {
        Route::get('/', [App\Http\Controllers\Staff\BookIssueController::class, 'index'])->name('book-issues.index');
        Route::get('/create', [App\Http\Controllers\Staff\BookIssueController::class, 'create'])->name('book-issues.create');
        Route::post('/', [App\Http\Controllers\Staff\BookIssueController::class, 'store'])->name('book-issues.store');
        Route::get('/{bookIssue}', [App\Http\Controllers\Staff\BookIssueController::class, 'show'])->name('book-issues.show');
        Route::patch('/{bookIssue}/return', [App\Http\Controllers\Staff\BookIssueController::class, 'return'])->name('book-issues.return');
        Route::patch('/{bookIssue}/mark-overdue', [App\Http\Controllers\Staff\BookIssueController::class, 'markOverdue'])->name('book-issues.mark-overdue');
        Route::patch('/{bookIssue}/mark-lost', [App\Http\Controllers\Staff\BookIssueController::class, 'markLost'])->name('book-issues.mark-lost');
        Route::put('/{bookIssue}/update-fine', [App\Http\Controllers\Staff\BookIssueController::class, 'updateFine'])->name('book-issues.update-fine');
    });
    
    // Notice routes
    Route::get('/notices', function () {
        $notices = \App\Models\Notice::where('is_published', true)
            ->where(function($query) {
                $query->whereJsonContains('target_roles', 'staff')
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
        
        return view('staff.notices.index', compact('notices'));
    })->name('notices.index');
});
```

### **Step 2: Department Head Routes**

**File: `routes/web.php` (Department Head section)**

```php
// Department Head Routes (for teachers who are department heads)
Route::prefix('department-head')->name('department-head.')->middleware(['auth', 'prevent.back'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DepartmentHead\DashboardController::class, 'index'])->name('dashboard');
    
    // Profile routes (using teacher profile controller)
    Route::prefix('profile')->group(function () {
        Route::get('/', [App\Http\Controllers\Teacher\ProfileController::class, 'index'])->name('profile.index');
        Route::get('/edit', [App\Http\Controllers\Teacher\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/update', [App\Http\Controllers\Teacher\ProfileController::class, 'update'])->name('profile.update');
        Route::get('/change-password', [App\Http\Controllers\Teacher\ProfileController::class, 'changePassword'])->name('profile.change-password');
        Route::put('/update-password', [App\Http\Controllers\Teacher\ProfileController::class, 'updatePassword'])->name('profile.update-password');
    });
    
    // Notice routes for department heads
    Route::prefix('notices')->group(function () {
        Route::get('/', [App\Http\Controllers\DepartmentHead\NoticeController::class, 'index'])->name('notices.index');
        Route::get('/create', [App\Http\Controllers\DepartmentHead\NoticeController::class, 'create'])->name('notices.create');
        Route::post('/', [App\Http\Controllers\DepartmentHead\NoticeController::class, 'store'])->name('notices.store');
        Route::get('/{notice}', [App\Http\Controllers\DepartmentHead\NoticeController::class, 'show'])->name('notices.show');
        Route::get('/{notice}/edit', [App\Http\Controllers\DepartmentHead\NoticeController::class, 'edit'])->name('notices.edit');
        Route::put('/{notice}', [App\Http\Controllers\DepartmentHead\NoticeController::class, 'update'])->name('notices.update');
        Route::delete('/{notice}', [App\Http\Controllers\DepartmentHead\NoticeController::class, 'destroy'])->name('notices.destroy');
        Route::patch('/{notice}/toggle-status', [App\Http\Controllers\DepartmentHead\NoticeController::class, 'toggleStatus'])->name('notices.toggle-status');
    });
    
    // Course Assignment routes
    Route::prefix('course-assignment')->name('course-assignment.')->group(function () {
        Route::get('/', [App\Http\Controllers\DepartmentHead\CourseAssignmentController::class, 'index'])->name('index');
        Route::get('/{course}/assign', [App\Http\Controllers\DepartmentHead\CourseAssignmentController::class, 'assign'])->name('assign');
        Route::put('/{course}/update-assignment', [App\Http\Controllers\DepartmentHead\CourseAssignmentController::class, 'updateAssignment'])->name('update-assignment');
        Route::delete('/{course}/unassign', [App\Http\Controllers\DepartmentHead\CourseAssignmentController::class, 'unassign'])->name('unassign');
        Route::post('/bulk-assign', [App\Http\Controllers\DepartmentHead\CourseAssignmentController::class, 'bulkAssign'])->name('bulk-assign');
        Route::get('/workload-report', [App\Http\Controllers\DepartmentHead\CourseAssignmentController::class, 'workloadReport'])->name('workload-report');
    });
});
```

### **Step 3: Git Commit**

```bash
git add .
git commit -m "Phase 6 complete: Staff and Department Head modules with library management"
```

---

## ✅ **Phase 6 Checklist**

- [x] Staff dashboard with library statistics
- [x] Book management system (CRUD)
- [x] Book issue/return system
- [x] Overdue book tracking
- [x] Fine management
- [x] Department head dashboard
- [x] Course assignment management
- [x] Faculty workload reporting
- [x] Notice management for department heads
- [x] Department statistics
- [x] Teacher oversight system
- [x] All staff and department head routes configured
- [x] Authorization checks implemented

---

## 🚀 **Next Steps**

Phase 6 is complete! You now have:
- Complete staff module with library management
- Book issue/return system with fine tracking
- Department head module with course assignment
- Faculty workload reporting
- Notice management for department heads
- Department statistics and oversight

**Ready for Phase 7?** We'll add advanced features, testing, and optimization! 🎯
