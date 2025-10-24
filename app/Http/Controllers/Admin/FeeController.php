<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Models\Student;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fees = Fee::with(['student.user', 'student.department'])
            ->latest()
            ->paginate(15);

        return view('admin.fees.index', compact('fees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $students = Student::where('is_active', true)->with('user')->get();
        return view('admin.fees.create', compact('students'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'fee_type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string|max:500',
        ]);

        Fee::create([
            'student_id' => $request->student_id,
            'fee_type' => $request->fee_type,
            'amount' => $request->amount,
            'paid_amount' => 0, // Initialize paid amount to 0
            'due_date' => $request->due_date,
            'paid_date' => null, // No payment date initially
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.fees.index')->with('success', 'Fee record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Fee $fee)
    {
        $fee->load(['student.user', 'student.department']);
        return view('admin.fees.show', compact('fee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Fee $fee)
    {
        $students = Student::where('is_active', true)->with('user')->get();
        return view('admin.fees.edit', compact('fee', 'students'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Fee $fee)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'fee_type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'due_date' => 'required|date',
            'paid_date' => 'nullable|date',
            'status' => 'required|in:pending,paid,partial,overdue',
            'notes' => 'nullable|string|max:500',
        ]);

        $fee->update([
            'student_id' => $request->student_id,
            'fee_type' => $request->fee_type,
            'amount' => $request->amount,
            'paid_amount' => $request->paid_amount ?? 0,
            'due_date' => $request->due_date,
            'paid_date' => $request->paid_date,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.fees.index')->with('success', 'Fee record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fee $fee)
    {
        $fee->delete();
        return redirect()->route('admin.fees.index')->with('success', 'Fee record deleted successfully.');
    }

    /**
     * Mark fee as paid.
     */
    public function markPaid(Fee $fee)
    {
        $fee->update([
            'status' => 'paid',
            'paid_amount' => $fee->amount, // Set paid amount to full amount
            'paid_date' => now(),
        ]);

        return back()->with('success', 'Fee marked as paid successfully.');
    }

    /**
     * Get fee statistics.
     */
    public function statistics()
    {
        $stats = [
            'total_fees' => Fee::sum('amount'),
            'paid_fees' => Fee::where('status', 'paid')->sum('amount'),
            'unpaid_fees' => Fee::where('status', 'unpaid')->sum('amount'),
            'overdue_fees' => Fee::where('status', 'unpaid')->where('due_date', '<', now())->sum('amount'),
            'total_students_with_fees' => Fee::distinct('student_id')->count(),
        ];

        return view('admin.fees.statistics', compact('stats'));
    }
}