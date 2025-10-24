<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notices = Notice::with('user')
            ->latest()
            ->paginate(15);

        return view('admin.notices.index', compact('notices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.notices.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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

        Notice::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'content' => $request->content,
            'type' => $request->type,
            'priority' => $request->priority,
            'target_roles' => $request->target_roles ?? ['all'],
            'publish_date' => $request->publish_date,
            'expiry_date' => $request->expiry_date,
            'is_published' => $request->has('is_published'),
            'is_pinned' => $request->has('is_pinned'),
        ]);

        return redirect()->route('admin.notices.index')->with('success', 'Notice created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Notice $notice)
    {
        $notice->load('user');
        return view('admin.notices.show', compact('notice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notice $notice)
    {
        return view('admin.notices.edit', compact('notice'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Notice $notice)
    {
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
            'target_roles' => $request->target_roles ?? ['all'],
            'publish_date' => $request->publish_date,
            'expiry_date' => $request->expiry_date,
            'is_published' => $request->has('is_published'),
            'is_pinned' => $request->has('is_pinned'),
        ]);

        return redirect()->route('admin.notices.index')->with('success', 'Notice updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notice $notice)
    {
        $notice->delete();
        return redirect()->route('admin.notices.index')->with('success', 'Notice deleted successfully.');
    }

    /**
     * Toggle the active status of the specified notice.
     */
    public function toggleStatus(Notice $notice)
    {
        $notice->is_active = !$notice->is_active;
        $notice->save();

        return back()->with('success', 'Notice status updated successfully.');
    }

    /**
     * Publish a notice.
     */
    public function publish(Notice $notice)
    {
        $notice->update([
            'published_at' => now(),
            'is_active' => true,
        ]);

        return back()->with('success', 'Notice published successfully.');
    }
}