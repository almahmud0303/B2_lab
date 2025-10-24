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

        return view('teacher.notices.show', compact('notice', 'teacher'));
    }
}
