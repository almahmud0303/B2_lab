<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LibraryController extends Controller
{
    public function index(Request $request)
    {
        $staff = Auth::user()->staff;
        
        if (!$staff) {
            abort(404, 'Staff profile not found');
        }

        $query = Book::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%")
                  ->orWhere('publisher', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        // Filter by availability
        if ($request->has('availability')) {
            if ($request->availability === 'available') {
                $query->where('available_copies', '>', 0);
            } elseif ($request->availability === 'unavailable') {
                $query->where('available_copies', '=', 0);
            }
        }

        $books = $query->orderBy('title')->paginate(20);
        
        $categories = Book::select('category')->distinct()->pluck('category');

        return view('staff.library.index', compact('books', 'categories', 'staff'));
    }

    public function create()
    {
        $staff = Auth::user()->staff;
        
        if (!$staff) {
            abort(404, 'Staff profile not found');
        }

        return view('staff.library.create', compact('staff'));
    }

    public function store(Request $request)
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

        return redirect()->route('staff.library.index')->with('success', 'Book added successfully.');
    }

    public function show($id)
    {
        $staff = Auth::user()->staff;
        
        if (!$staff) {
            abort(404, 'Staff profile not found');
        }

        $book = Book::with('bookIssues.student.user')->findOrFail($id);

        return view('staff.library.show', compact('book', 'staff'));
    }

    public function edit($id)
    {
        $staff = Auth::user()->staff;
        
        if (!$staff) {
            abort(404, 'Staff profile not found');
        }

        $book = Book::findOrFail($id);

        return view('staff.library.edit', compact('book', 'staff'));
    }

    public function update(Request $request, $id)
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

        return redirect()->route('staff.library.index')->with('success', 'Book updated successfully.');
    }

    public function destroy($id)
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

        return redirect()->route('staff.library.index')->with('success', 'Book deleted successfully.');
    }
}
