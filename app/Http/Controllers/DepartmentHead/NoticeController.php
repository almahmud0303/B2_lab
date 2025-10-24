<?php

namespace App\Http\Controllers\DepartmentHead;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoticeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departmentHead = Auth::user()->teacher;
        
        if (!$departmentHead || !$departmentHead->department || !$departmentHead->is_department_head) {
            abort(404, 'Department head profile not found');
        }

        $notices = Notice::with('user')
            ->where('user_id', Auth::id())
            ->where(function($query) use ($departmentHead) {
                $query->whereJsonContains('target_roles', 'student')
                      ->orWhereJsonContains('target_roles', 'teacher')
                      ->orWhereJsonContains('target_roles', 'staff');
            })
            ->latest()
            ->paginate(15);

        return view('department-head.notices.index', compact('notices', 'departmentHead'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departmentHead = Auth::user()->teacher;
        
        if (!$departmentHead || !$departmentHead->department || !$departmentHead->is_department_head) {
            abort(404, 'Department head profile not found');
        }

        return view('department-head.notices.create', compact('departmentHead'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $departmentHead = Auth::user()->teacher;
        
        if (!$departmentHead || !$departmentHead->department || !$departmentHead->is_department_head) {
            abort(404, 'Department head profile not found');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:general,academic,exam,fee,library,event',
            'priority' => 'required|in:low,medium,high,urgent',
            'target_roles' => 'nullable|array',
            'target_roles.*' => 'in:student,teacher,staff,admin',
            'publish_date' => 'required|date|after_or_equal:today',
            'expiry_date' => 'nullable|date|after:publish_date',
            'is_published' => 'boolean',
            'is_pinned' => 'boolean',
        ]);

        // Automatically target department members
        $targetRoles = $request->target_roles ?? ['student', 'teacher'];
        
        Notice::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
            'type' => $request->type,
            'priority' => $request->priority,
            'target_roles' => $targetRoles,
            'publish_date' => $request->publish_date,
            'expiry_date' => $request->expiry_date,
            'is_published' => $request->has('is_published'),
            'is_pinned' => $request->has('is_pinned'),
        ]);

        return redirect()->route('department-head.notices.index')->with('success', 'Notice created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Notice $notice)
    {
        $departmentHead = Auth::user()->teacher;
        
        if (!$departmentHead || !$departmentHead->department || !$departmentHead->is_department_head) {
            abort(404, 'Department head profile not found');
        }

        // Ensure the notice belongs to this department head
        if ($notice->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to notice.');
        }

        $notice->load('user');
        return view('department-head.notices.show', compact('notice', 'departmentHead'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notice $notice)
    {
        $departmentHead = Auth::user()->teacher;
        
        if (!$departmentHead || !$departmentHead->department || !$departmentHead->is_department_head) {
            abort(404, 'Department head profile not found');
        }

        // Ensure the notice belongs to this department head
        if ($notice->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to notice.');
        }

        return view('department-head.notices.edit', compact('notice', 'departmentHead'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Notice $notice)
    {
        $departmentHead = Auth::user()->teacher;
        
        if (!$departmentHead || !$departmentHead->department || !$departmentHead->is_department_head) {
            abort(404, 'Department head profile not found');
        }

        // Ensure the notice belongs to this department head
        if ($notice->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to notice.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:general,academic,exam,fee,library,event',
            'priority' => 'required|in:low,medium,high,urgent',
            'target_roles' => 'nullable|array',
            'target_roles.*' => 'in:student,teacher,staff,admin',
            'publish_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:publish_date',
            'is_published' => 'boolean',
            'is_pinned' => 'boolean',
        ]);

        $notice->update([
            'title' => $request->title,
            'content' => $request->content,
            'type' => $request->type,
            'priority' => $request->priority,
            'target_roles' => $request->target_roles ?? ['student', 'teacher'],
            'publish_date' => $request->publish_date,
            'expiry_date' => $request->expiry_date,
            'is_published' => $request->has('is_published'),
            'is_pinned' => $request->has('is_pinned'),
        ]);

        return redirect()->route('department-head.notices.index')->with('success', 'Notice updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notice $notice)
    {
        $departmentHead = Auth::user()->teacher;
        
        if (!$departmentHead || !$departmentHead->department || !$departmentHead->is_department_head) {
            abort(404, 'Department head profile not found');
        }

        // Ensure the notice belongs to this department head
        if ($notice->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to notice.');
        }

        $notice->delete();
        return redirect()->route('department-head.notices.index')->with('success', 'Notice deleted successfully.');
    }

    /**
     * Toggle the published status of the specified notice.
     */
    public function toggleStatus(Notice $notice)
    {
        $departmentHead = Auth::user()->teacher;
        
        if (!$departmentHead || !$departmentHead->department || !$departmentHead->is_department_head) {
            abort(404, 'Department head profile not found');
        }

        // Ensure the notice belongs to this department head
        if ($notice->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to notice.');
        }

        $notice->update([
            'is_published' => !$notice->is_published
        ]);

        $status = $notice->is_published ? 'published' : 'unpublished';
        return redirect()->back()->with('success', "Notice {$status} successfully.");
    }
}
