<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookIssue;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LibraryController extends Controller
{
    public function index(Request $request)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Get student's book issues
        $issuedBooks = $student->bookIssues()
            ->with('book')
            ->where('status', 'issued')
            ->orderBy('due_date')
            ->get();

        $returnedBooks = $student->bookIssues()
            ->with('book')
            ->where('status', 'returned')
            ->orderBy('return_date', 'desc')
            ->limit(10)
            ->get();

        $overdueBooks = $student->bookIssues()
            ->with('book')
            ->where('status', 'overdue')
            ->orderBy('due_date')
            ->get();

        // Get available books
        $query = Book::where('is_active', true);
        
        // Apply search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Apply availability filter
        if ($request->has('availability') && $request->availability === 'available') {
            $query->where('available_copies', '>', 0);
        }

        // Apply category filter
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        $books = $query->orderBy('title')->paginate(12);

        // For the view variables
        $myBooks = $issuedBooks;

        // Get all categories for filter
        $categories = Book::where('is_active', true)
            ->distinct()
            ->pluck('category')
            ->sort();

        return view('student.library.index', compact('issuedBooks', 'returnedBooks', 'overdueBooks', 'myBooks', 'books', 'student', 'categories'));
    }

    public function search(Request $request)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        $query = $request->get('q');
        $books = collect();

        if ($query) {
            $books = Book::where('is_active', true)
                ->where(function($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('author', 'like', "%{$query}%")
                      ->orWhere('isbn', 'like', "%{$query}%");
                })
                ->where('available_copies', '>', 0)
                ->paginate(15);
        }

        return view('student.library.search', compact('books', 'query', 'student'));
    }

    public function show(Book $book)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Check if student has already issued this book
        $currentIssue = $student->bookIssues()
            ->where('book_id', $book->id)
            ->whereIn('status', ['issued', 'overdue'])
            ->first();

        // Get book issue history for this student
        $issueHistory = $student->bookIssues()
            ->where('book_id', $book->id)
            ->orderBy('issue_date', 'desc')
            ->get();

        return view('student.library.show', compact('book', 'currentIssue', 'issueHistory', 'student'));
    }

    public function requestBook(Request $request, Book $book)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Check if book is available
        if ($book->available_copies <= 0) {
            return back()->with('error', 'This book is currently not available.');
        }

        // Check if student already has this book issued
        $existingIssue = $student->bookIssues()
            ->where('book_id', $book->id)
            ->whereIn('status', ['issued', 'overdue'])
            ->first();

        if ($existingIssue) {
            return back()->with('error', 'You already have this book issued.');
        }

        // Check maximum book limit (e.g., 5 books per student)
        $issuedCount = $student->bookIssues()
            ->whereIn('status', ['issued', 'overdue'])
            ->count();

        if ($issuedCount >= 5) {
            return back()->with('error', 'You have reached the maximum limit of 5 books. Please return some books first.');
        }

        // Check for overdue books
        $overdueCount = $student->bookIssues()
            ->where('status', 'overdue')
            ->count();

        if ($overdueCount > 0) {
            return back()->with('error', 'You have overdue books. Please return them before issuing new books.');
        }

        // Get a library staff member (or use first staff member)
        $libraryStaff = \App\Models\User::where('role', 'staff')->first();
        
        if (!$libraryStaff) {
            return back()->with('error', 'Library staff not available. Please contact administration.');
        }

        // Create book issue request (this would typically go through staff approval)
        BookIssue::create([
            'book_id' => $book->id,
            'student_id' => $student->id,
            'staff_id' => $libraryStaff->id,
            'issue_date' => now(),
            'due_date' => now()->addDays(14), // 14 days loan period
            'status' => 'issued',
            'notes' => 'Self-service book issue via student portal',
        ]);

        // Decrement available copies
        $book->decrement('available_copies');

        return back()->with('success', 'Book issued successfully. Due date: ' . now()->addDays(14)->format('M d, Y'));
    }

    public function returnBook(BookIssue $bookIssue)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Ensure the book issue belongs to the student
        if ($bookIssue->student_id !== $student->id) {
            abort(403, 'Unauthorized access to book issue record');
        }

        // Check if book is already returned
        if ($bookIssue->status === 'returned') {
            return back()->with('error', 'This book has already been returned.');
        }

        // Update book issue
        $bookIssue->update([
            'return_date' => now(),
            'status' => 'returned',
        ]);

        // Increment available copies
        $bookIssue->book->increment('available_copies');

        // Calculate fine if overdue
        $fine = 0;
        if ($bookIssue->due_date < now()) {
            $daysOverdue = now()->diffInDays($bookIssue->due_date);
            $fine = $daysOverdue * 1; // $1 per day fine
        }

        $message = 'Book returned successfully.';
        if ($fine > 0) {
            $message .= " Fine for overdue: $" . $fine;
        }

        return back()->with('success', $message);
    }

    public function renewBook(BookIssue $bookIssue)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Ensure the book issue belongs to the student
        if ($bookIssue->student_id !== $student->id) {
            abort(403, 'Unauthorized access to book issue record');
        }

        // Check if book is issued and not overdue
        if ($bookIssue->status !== 'issued') {
            return back()->with('error', 'This book cannot be renewed.');
        }

        // Check if book is overdue
        if ($bookIssue->due_date < now()) {
            return back()->with('error', 'Cannot renew overdue books. Please return and re-issue.');
        }

        // Extend due date by 14 days
        $bookIssue->update([
            'due_date' => $bookIssue->due_date->addDays(14),
        ]);

        return back()->with('success', 'Book renewed successfully. New due date: ' . $bookIssue->due_date->format('M d, Y'));
    }

    public function fines()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        $overdueBooks = $student->bookIssues()
            ->with('book')
            ->where('status', 'overdue')
            ->get();

        $totalFine = 0;
        foreach ($overdueBooks as $bookIssue) {
            $daysOverdue = now()->diffInDays($bookIssue->due_date);
            $fine = $daysOverdue * 1; // $1 per day fine
            $bookIssue->fine_amount = $fine;
            $totalFine += $fine;
        }

        return view('student.library.fines', compact('overdueBooks', 'totalFine', 'student'));
    }

    public function history()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        $bookHistory = $student->bookIssues()
            ->with('book')
            ->orderBy('issue_date', 'desc')
            ->paginate(20);

        return view('student.library.history', compact('bookHistory', 'student'));
    }

    public function myBooks()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Get currently borrowed books
        $borrowedBooks = $student->bookIssues()
            ->with('book')
            ->where('status', 'issued')
            ->orderBy('due_date')
            ->get();

        // Get overdue books
        $overdueBooks = $student->bookIssues()
            ->with('book')
            ->where('status', 'overdue')
            ->orderBy('due_date')
            ->get();

        // Get borrowing history
        $history = $student->bookIssues()
            ->with('book')
            ->orderBy('issued_date', 'desc')
            ->paginate(15);

        // Calculate statistics
        $totalBorrowed = $student->bookIssues()->count();
        $returnedCount = $student->bookIssues()->where('status', 'returned')->count();
        $currentlyOut = $borrowedBooks->count();

        return view('student.library.my-books', compact('borrowedBooks', 'overdueBooks', 'history', 'totalBorrowed', 'returnedCount', 'currentlyOut'));
    }


    public function rules()
    {
        return view('student.library.rules');
    }
}