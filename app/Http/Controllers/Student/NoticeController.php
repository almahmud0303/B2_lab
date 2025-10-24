<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notice;
use App\Models\Student;

class NoticeController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Get notices for students
        $notices = Notice::where('is_published', true)
            ->where(function($query) {
                $query->whereJsonContains('target_roles', 'student')
                      ->orWhereJsonContains('target_roles', 'all')
                      ->orWhereNull('target_roles');
            })
            ->where(function($query) {
                $query->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>=', now());
            })
            ->orderBy('is_pinned', 'desc')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Get pinned notices
        $pinnedNotices = Notice::where('is_published', true)
            ->where('is_pinned', true)
            ->where(function($query) {
                $query->whereJsonContains('target_roles', 'student')
                      ->orWhereJsonContains('target_roles', 'all')
                      ->orWhereNull('target_roles');
            })
            ->where(function($query) {
                $query->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>=', now());
            })
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get recent notices (last 7 days)
        $recentNotices = Notice::where('is_published', true)
            ->where(function($query) {
                $query->whereJsonContains('target_roles', 'student')
                      ->orWhereJsonContains('target_roles', 'all')
                      ->orWhereNull('target_roles');
            })
            ->where('created_at', '>=', now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('student.notices.index', compact(
            'notices',
            'pinnedNotices',
            'recentNotices'
        ));
    }

    public function show(Notice $notice)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Check if notice is accessible to students
        if (!$notice->is_published) {
            abort(404, 'Notice not found');
        }

        if ($notice->expiry_date && $notice->expiry_date < now()) {
            abort(404, 'Notice has expired');
        }

        // Check if notice is targeted to students
        $targetRoles = $notice->target_roles ?? [];
        if (!in_array('student', $targetRoles) && !in_array('all', $targetRoles) && !empty($targetRoles)) {
            abort(403, 'You do not have access to this notice');
        }

        // Load notice with relationships
        $notice->load('user');

        // Get related notices
        $relatedNotices = Notice::where('is_published', true)
            ->where('id', '!=', $notice->id)
            ->where('type', $notice->type)
            ->where(function($query) {
                $query->whereJsonContains('target_roles', 'student')
                      ->orWhereJsonContains('target_roles', 'all')
                      ->orWhereNull('target_roles');
            })
            ->where(function($query) {
                $query->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>=', now());
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('student.notices.show', compact('notice', 'relatedNotices'));
    }

    public function byType($type)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        $validTypes = ['general', 'academic', 'exam', 'fee', 'library', 'event'];
        if (!in_array($type, $validTypes)) {
            abort(404, 'Invalid notice type');
        }

        $notices = Notice::where('is_published', true)
            ->where('type', $type)
            ->where(function($query) {
                $query->whereJsonContains('target_roles', 'student')
                      ->orWhereJsonContains('target_roles', 'all')
                      ->orWhereNull('target_roles');
            })
            ->where(function($query) {
                $query->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>=', now());
            })
            ->orderBy('is_pinned', 'desc')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('student.notices.by-type', compact('notices', 'type'));
    }

    public function urgent()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        $urgentNotices = Notice::where('is_published', true)
            ->where('priority', 'urgent')
            ->where(function($query) {
                $query->whereJsonContains('target_roles', 'student')
                      ->orWhereJsonContains('target_roles', 'all')
                      ->orWhereNull('target_roles');
            })
            ->where(function($query) {
                $query->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>=', now());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('student.notices.urgent', compact('urgentNotices'));
    }

    public function search(Request $request)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        $query = $request->get('q');
        $type = $request->get('type');
        $priority = $request->get('priority');

        $notices = Notice::where('is_published', true)
            ->where(function($q) use ($query) {
                if ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('content', 'like', "%{$query}%");
                }
            })
            ->when($type, function($q) use ($type) {
                $q->where('type', $type);
            })
            ->when($priority, function($q) use ($priority) {
                $q->where('priority', $priority);
            })
            ->where(function($query) {
                $query->whereJsonContains('target_roles', 'student')
                      ->orWhereJsonContains('target_roles', 'all')
                      ->orWhereNull('target_roles');
            })
            ->where(function($query) {
                $query->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>=', now());
            })
            ->orderBy('is_pinned', 'desc')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('student.notices.search', compact('notices', 'query', 'type', 'priority'));
    }

    public function downloadAttachment(Notice $notice)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Check if notice is accessible to students
        if (!$notice->is_published) {
            abort(404, 'Notice not found');
        }

        if ($notice->expiry_date && $notice->expiry_date < now()) {
            abort(404, 'Notice has expired');
        }

        // Check if notice is targeted to students
        $targetRoles = $notice->target_roles ?? [];
        if (!in_array('student', $targetRoles) && !in_array('all', $targetRoles) && !empty($targetRoles)) {
            abort(403, 'You do not have access to this notice');
        }

        // Check if notice has attachment
        if (!$notice->attachment_path) {
            abort(404, 'No attachment found');
        }

        // In a real implementation, you would:
        // 1. Check if file exists
        // 2. Log download activity
        // 3. Return file for download

        // For now, return a placeholder response
        return response()->json([
            'message' => 'Download functionality will be implemented with file storage system.'
        ]);
    }

    public function markAsRead(Notice $notice)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // In a real implementation, you would:
        // 1. Create a notice_reads table
        // 2. Mark the notice as read for this student
        // 3. Track read status

        // For now, return success
        return response()->json(['success' => true]);
    }

    public function getUnreadCount()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            return response()->json(['count' => 0]);
        }

        // In a real implementation, you would count unread notices
        // For now, return a placeholder
        return response()->json(['count' => 0]);
    }
}
