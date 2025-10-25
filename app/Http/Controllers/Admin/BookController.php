<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookIssue;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BookController extends Controller
{
// Display the all  books
    public function index()
    {
        $books = Book::with(['bookIssues.student.user'])
            ->latest()
            ->paginate(15);

        return view('admin.books.index', compact('books'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.books.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|max:20|unique:books,isbn',
            'publisher' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|min:1800|max:' . date('Y'),
            'category' => 'nullable|string|max:100',
            'total_copies' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
        ]);

        Book::create([
            'title' => $request->title,
            'author' => $request->author,
            'isbn' => $request->isbn,
            'publisher' => $request->publisher,
            'publication_year' => $request->publication_year,
            'category' => $request->category,
            'total_copies' => $request->total_copies,
            'available_copies' => $request->total_copies,
            'description' => $request->description,
            'price' => $request->price,
            'is_active' => true,
        ]);

        return redirect()->route('admin.books.index')->with('success', 'Book added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        $book->load(['bookIssues.student.user', 'bookIssues.staff']);
        
        // Get book statistics
        $stats = [
            'total_issues' => $book->bookIssues->count(),
            'active_issues' => $book->bookIssues->where('status', 'issued')->count(),
            'returned_books' => $book->bookIssues->where('status', 'returned')->count(),
            'overdue_books' => $book->bookIssues->where('status', 'overdue')->count(),
        ];

        // Get recent book issues
        $recentIssues = $book->bookIssues()
            ->with(['student.user', 'staff'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.books.show', compact('book', 'stats', 'recentIssues'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
        return view('admin.books.edit', compact('book'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => ['required', 'string', 'max:20', Rule::unique('books')->ignore($book->id)],
            'publisher' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|min:1800|max:' . date('Y'),
            'category' => 'nullable|string|max:100',
            'total_copies' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        // Calculate available copies based on active issues
        $activeIssues = $book->bookIssues()->where('status', 'issued')->count();
        $availableCopies = max(0, $request->total_copies - $activeIssues);

        $book->update([
            'title' => $request->title,
            'author' => $request->author,
            'isbn' => $request->isbn,
            'publisher' => $request->publisher,
            'publication_year' => $request->publication_year,
            'category' => $request->category,
            'total_copies' => $request->total_copies,
            'available_copies' => $availableCopies,
            'description' => $request->description,
            'price' => $request->price,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.books.index')->with('success', 'Book updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        $book->delete();
        return redirect()->route('admin.books.index')->with('success', 'Book deleted successfully.');
    }

    /**
     * Issue a book to a student.
     */
    public function issueBook(Request $request, Book $book)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'due_date' => 'required|date|after:today',
        ]);

        if ($book->available_copies <= 0) {
            return back()->with('error', 'No copies available for this book.');
        }

        BookIssue::create([
            'book_id' => $book->id,
            'student_id' => $request->student_id,
            'staff_id' => auth()->id(),
            'issue_date' => now(),
            'due_date' => $request->due_date,
            'status' => 'issued',
        ]);

        // Update available copies
        $book->decrement('available_copies');

        return back()->with('success', 'Book issued successfully.');
    }

    /**
     * Return a book.
     */
    public function returnBook(BookIssue $bookIssue)
    {
        $bookIssue->update([
            'return_date' => now(),
            'status' => 'returned',
        ]);

        // Update available copies
        $bookIssue->book->increment('available_copies');

        return back()->with('success', 'Book returned successfully.');
    }
}