<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoticeController extends Controller
{
    public function index()
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            abort(404, 'Teacher profile not found');
        }

        $notices = Notice::where('is_published', true)
            ->where(function($query) use ($teacher) {
                $query->whereNull('department_id')
                      ->orWhere('department_id', $teacher->department_id);
            })
            ->where(function($query) {
                $query->whereNull('target_roles')
                      ->orWhereJsonContains('target_roles', 'teacher')
                      ->orWhereJsonContains('target_roles', 'all');
            })
            ->where(function($query) {
                $query->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>=', now());
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('teacher.notices.index', compact('notices', 'teacher'));
    }

    public function show($id)
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            abort(404, 'Teacher profile not found');
        }

        $notice = Notice::where('is_published', true)->findOrFail($id);

        // Check department access - teachers can only see notices from their department or general notices
        if ($notice->department_id !== null && $notice->department_id !== $teacher->department_id) {
            abort(403, 'You do not have access to this notice');
        }

        // Check if notice is targeted to teachers
        $targetRoles = $notice->target_roles ?? [];
        if (!empty($targetRoles) && !in_array('teacher', $targetRoles) && !in_array('all', $targetRoles)) {
            abort(403, 'You do not have access to this notice');
        }

        return view('teacher.notices.show', compact('notice', 'teacher'));
    }
}
