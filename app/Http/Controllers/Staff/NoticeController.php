<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoticeController extends Controller
{
    public function index()
    {
        $staff = Auth::user()->staff;
        
        if (!$staff) {
            abort(404, 'Staff profile not found');
        }

        $notices = Notice::where('is_published', true)
            ->where(function($query) {
                $query->whereNull('target_roles')
                      ->orWhereJsonContains('target_roles', 'staff')
                      ->orWhereJsonContains('target_roles', 'all');
            })
            ->where(function($query) {
                $query->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>=', now());
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('staff.notices.index', compact('notices', 'staff'));
    }

    public function show($id)
    {
        $staff = Auth::user()->staff;
        
        if (!$staff) {
            abort(404, 'Staff profile not found');
        }

        $notice = Notice::where('is_published', true)->findOrFail($id);

        return view('staff.notices.show', compact('notice', 'staff'));
    }
}
