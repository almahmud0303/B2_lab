<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookIssue;
use App\Models\Hall;
use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Get library statistics
        $libraryStats = [
            'total_books' => Book::count(),
            'available_books' => Book::where('available_copies', '>', 0)->count(),
            'issued_books' => BookIssue::where('status', 'issued')->count(),
            'overdue_books' => BookIssue::where('status', 'overdue')->count(),
        ];

        // Get hall statistics
        $hallStats = [
            'total_halls' => Hall::count(),
            'available_halls' => Hall::where('is_available', true)->count(),
            'occupied_halls' => Hall::where('is_available', false)->count(),
        ];

        // Get recent book issues
        $recentBookIssues = BookIssue::with('student.user', 'book', 'staff')
            ->latest()
            ->limit(10)
            ->get();

        // Get overdue books
        $overdueBooks = BookIssue::with('student.user', 'book')
            ->where('status', 'overdue')
            ->orWhere(function($query) {
                $query->where('status', 'issued')
                      ->where('due_date', '<', now());
            })
            ->latest()
            ->limit(10)
            ->get();

        // Get recent notices for staff
        $recentNotices = Notice::where('is_published', true)
            ->where(function($query) {
                $query->whereNull('target_roles')
                      ->orWhereJsonContains('target_roles', 'staff');
            })
            ->where(function($query) {
                $query->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>=', now());
            })
            ->latest()
            ->limit(5)
            ->get();

        // Get available halls
        $availableHalls = Hall::where('is_available', true)
            ->where('is_active', true)
            ->get();

        return view('staff.dashboard', compact(
            'libraryStats',
            'hallStats',
            'recentBookIssues',
            'overdueBooks',
            'recentNotices',
            'availableHalls'
        ));
    }
}