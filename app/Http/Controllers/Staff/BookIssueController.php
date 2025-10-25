<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookIssue;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookIssueController extends Controller
{
    public function index(Request $request)
    {
        $staff = Auth::user()->staff;
        
        if (!$staff) {
            abort(404, 'Staff profile not found');
        }

        $query = BookIssue::with('student.user', 'book', 'staff.user');

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Search by student or book
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('student.user', function($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('student', function($subQ) use ($search) {
                    $subQ->where('student_id', 'like', "%{$search}%");
                })
                ->orWhereHas('book', function($subQ) use ($search) {
                    $subQ->where('title', 'like', "%{$search}%")
                         ->orWhere('isbn', 'like', "%{$search}%");
                });
            });
        }

        $bookIssues = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('staff.book-issues.index', compact('bookIssues', 'staff'));
    }

    public function create()
    {
        $staff = Auth::user()->staff;
        
        if (!$staff) {
            abort(404, 'Staff profile not found');
        }

        $students = Student::with('user')->where('is_active', true)->get();
        $books = Book::where('available_copies', '>', 0)->get();

        return view('staff.book-issues.create', compact('students', 'books', 'staff'));
    }

    public function store(Request $request)
    {
        $staff = Auth::user()->staff;
        
        if (!$staff) {
            abort(404, 'Staff profile not found');
        }

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'book_id' => 'required|exists:books,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after:issue_date',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $book = Book::findOrFail($request->book_id);

            // Check if book is available
            if ($book->available_copies <= 0) {
                return redirect()->back()->with('error', 'Book is not available.');
            }

            // Check if student already has this book
            $existingIssue = BookIssue::where('student_id', $request->student_id)
                ->where('book_id', $request->book_id)
                ->whereIn('status', ['issued', 'overdue'])
                ->first();

            if ($existingIssue) {
                return redirect()->back()->with('error', 'Student already has this book issued.');
            }

            // Create book issue
            BookIssue::create([
                'student_id' => $request->student_id,
                'book_id' => $request->book_id,
                'staff_id' => $staff->id,
                'issue_date' => $request->issue_date,
                'due_date' => $request->due_date,
                'status' => 'issued',
                'notes' => $request->notes,
            ]);

            // Decrease available copies
            $book->decrement('available_copies');

            DB::commit();
            return redirect()->route('staff.book-issues.index')->with('success', 'Book issued successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error issuing book: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $staff = Auth::user()->staff;
        
        if (!$staff) {
            abort(404, 'Staff profile not found');
        }

        $bookIssue = BookIssue::with('student.user', 'book', 'staff.user')->findOrFail($id);

        return view('staff.book-issues.show', compact('bookIssue', 'staff'));
    }

    public function approve($id)
    {
        $staff = Auth::user()->staff;
        
        if (!$staff) {
            abort(404, 'Staff profile not found');
        }

        $bookIssue = BookIssue::with('book')->findOrFail($id);

        if ($bookIssue->status !== 'requested') {
            return redirect()->back()->with('error', 'This request cannot be approved.');
        }

        DB::beginTransaction();
        try {
            $book = $bookIssue->book;

            if ($book->available_copies <= 0) {
                return redirect()->back()->with('error', 'Book is not available.');
            }

            $bookIssue->update([
                'status' => 'issued',
                'staff_id' => $staff->id,
                'issue_date' => now(),
            ]);

            $book->decrement('available_copies');

            DB::commit();
            return redirect()->back()->with('success', 'Book issue approved.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error approving book issue: ' . $e->getMessage());
        }
    }

    public function reject($id)
    {
        $staff = Auth::user()->staff;
        
        if (!$staff) {
            abort(404, 'Staff profile not found');
        }

        $bookIssue = BookIssue::findOrFail($id);

        if ($bookIssue->status !== 'requested') {
            return redirect()->back()->with('error', 'This request cannot be rejected.');
        }

        $bookIssue->update([
            'status' => 'rejected',
            'staff_id' => $staff->id,
        ]);

        return redirect()->back()->with('success', 'Book issue rejected.');
    }

    public function return($id)
    {
        $staff = Auth::user()->staff;
        
        if (!$staff) {
            abort(404, 'Staff profile not found');
        }

        $bookIssue = BookIssue::with('book')->findOrFail($id);

        if (!in_array($bookIssue->status, ['issued', 'overdue'])) {
            return redirect()->back()->with('error', 'This book is not currently issued.');
        }

        DB::beginTransaction();
        try {
            $bookIssue->update([
                'status' => 'returned',
                'return_date' => now(),
            ]);

            $bookIssue->book->increment('available_copies');

            DB::commit();
            return redirect()->back()->with('success', 'Book returned successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error returning book: ' . $e->getMessage());
        }
    }

    public function renew($id, Request $request)
    {
        $staff = Auth::user()->staff;
        
        if (!$staff) {
            abort(404, 'Staff profile not found');
        }

        $bookIssue = BookIssue::findOrFail($id);

        if ($bookIssue->status !== 'issued') {
            return redirect()->back()->with('error', 'Only issued books can be renewed.');
        }

        $request->validate([
            'new_due_date' => 'required|date|after:today',
        ]);

        $bookIssue->update([
            'due_date' => $request->new_due_date,
        ]);

        return redirect()->back()->with('success', 'Book renewed successfully.');
    }

    /**
     * Show the form for creating a new book.
     */
    public function createBook()
    {
        $staff = Auth::user()->staff;
        
        if (!$staff) {
            abort(404, 'Staff profile not found');
        }

        return view('staff.book-issues.create-book', compact('staff'));
    }

    /**
     * Store a newly created book.
     */
    public function storeBook(Request $request)
    {
        $staff = Auth::user()->staff;
        
        if (!$staff) {
            abort(404, 'Staff profile not found');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|max:50|unique:books',
            'publisher' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|min:1800|max:' . date('Y'),
            'category' => 'required|string|max:100',
            'total_copies' => 'required|integer|min:1',
            'available_copies' => 'required|integer|min:0',
            'shelf_location' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        Book::create($request->all());

        return redirect()->route('staff.book-issues.index')->with('success', 'Book added successfully.');
    }

    /**
     * Show the form for editing a book.
     */
    public function editBook($id)
    {
        $staff = Auth::user()->staff;
        
        if (!$staff) {
            abort(404, 'Staff profile not found');
        }

        $book = Book::findOrFail($id);

        return view('staff.book-issues.edit-book', compact('book', 'staff'));
    }

    /**
     * Update the specified book.
     */
    public function updateBook(Request $request, $id)
    {
        $staff = Auth::user()->staff;
        
        if (!$staff) {
            abort(404, 'Staff profile not found');
        }

        $book = Book::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|max:50|unique:books,isbn,' . $book->id,
            'publisher' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|min:1800|max:' . date('Y'),
            'category' => 'required|string|max:100',
            'total_copies' => 'required|integer|min:1',
            'available_copies' => 'required|integer|min:0',
            'shelf_location' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        $book->update($request->all());

        return redirect()->route('staff.book-issues.index')->with('success', 'Book updated successfully.');
    }

    /**
     * Remove the specified book.
     */
    public function destroyBook($id)
    {
        $staff = Auth::user()->staff;
        
        if (!$staff) {
            abort(404, 'Staff profile not found');
        }

        $book = Book::findOrFail($id);

        // Check if book has active issues
        if ($book->bookIssues()->whereIn('status', ['issued', 'overdue'])->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete book with active issues.');
        }

        $book->delete();

        return redirect()->route('staff.book-issues.index')->with('success', 'Book deleted successfully.');
    }
}
