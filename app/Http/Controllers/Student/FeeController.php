<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class FeeController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Get all fees for the student
        $fees = $student->fees()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Calculate fee statistics
        $totalFees = $student->fees()->sum('amount');
        $paidFees = $student->fees()->where('status', 'paid')->sum('paid_amount');
        $pendingFees = $student->fees()->where('status', 'pending')->sum('amount');
        $overdueFees = $student->fees()->where('status', 'overdue')->sum('amount');

        // Recent payments
        $recentPayments = $student->fees()
            ->where('status', 'paid')
            ->orderBy('paid_date', 'desc')
            ->limit(5)
            ->get();

        return view('student.fees.index', compact('fees', 'totalFees', 'paidFees', 'pendingFees', 'overdueFees', 'recentPayments', 'student'));
    }

    public function show(Fee $fee)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Ensure fee belongs to the student
        if ($fee->student_id !== $student->id) {
            abort(403, 'Unauthorized access to fee record');
        }

        return view('student.fees.show', compact('fee', 'student'));
    }

    public function paymentHistory()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        $payments = $student->fees()
            ->where('status', 'paid')
            ->orderBy('paid_date', 'desc')
            ->paginate(20);

        return view('student.fees.payment-history', compact('payments', 'student'));
    }

    public function downloadReceipt(Fee $fee)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Ensure fee belongs to the student and is paid
        if ($fee->student_id !== $student->id || $fee->status !== 'paid') {
            abort(403, 'Cannot download receipt for unpaid fee');
        }

        $pdf = Pdf::loadView('student.fees.receipt', compact('fee', 'student'));
        
        return $pdf->download('payment-receipt-' . $fee->id . '.pdf');
    }

    public function payOnline(Request $request, Fee $fee)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Ensure fee belongs to the student and is not paid
        if ($fee->student_id !== $student->id || $fee->status === 'paid') {
            abort(403, 'Cannot pay this fee');
        }

        $request->validate([
            'payment_method' => 'required|in:card,bank_transfer,mobile_banking',
            'transaction_id' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01|max:' . $fee->amount,
        ]);

        // Process payment (placeholder - integrate with actual payment gateway)
        $paymentSuccess = $this->processPaymentGateway($request->all(), $fee);

        if ($paymentSuccess) {
            // Update fee status
            $fee->update([
                'status' => $request->amount >= $fee->amount ? 'paid' : 'partial',
                'paid_amount' => $request->amount,
                'paid_date' => now(),
            ]);

            return back()->with('success', 'Payment successful! Receipt has been generated.');
        } else {
            return back()->with('error', 'Payment failed. Please try again.');
        }
    }


    public function pendingFees()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        $pendingFees = $student->fees()
            ->whereIn('status', ['pending', 'partial', 'overdue'])
            ->orderBy('due_date')
            ->get();

        return view('student.fees.pending', compact('pendingFees', 'student'));
    }

    public function overdueFees()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        $overdueFees = $student->fees()
            ->where('status', 'overdue')
            ->orWhere(function($query) {
                $query->where('status', 'pending')
                      ->where('due_date', '<', now());
            })
            ->orderBy('due_date')
            ->get();

        return view('student.fees.overdue', compact('overdueFees', 'student'));
    }

    public function history(Request $request)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Get paid fees (payment history)
        $query = $student->fees()->where('status', 'paid');

        // Filter by month if requested
        if ($request->has('month') && $request->month) {
            $month = \Carbon\Carbon::parse($request->month);
            $query->whereMonth('paid_date', $month->month)
                  ->whereYear('paid_date', $month->year);
        }

        $payments = $query->orderBy('paid_date', 'desc')->paginate(15);

        // Calculate statistics
        $totalPaid = $student->fees()->where('status', 'paid')->sum('paid_amount');
        $thisMonthPaid = $student->fees()
            ->where('status', 'paid')
            ->whereMonth('paid_date', now()->month)
            ->whereYear('paid_date', now()->year)
            ->sum('paid_amount');

        // Generate month options for filter
        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $months[] = [
                'value' => $date->format('Y-m'),
                'label' => $date->format('F Y')
            ];
        }

        return view('student.fees.history', compact('payments', 'totalPaid', 'thisMonthPaid', 'months'));
    }

    public function payment(Fee $fee)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Ensure fee belongs to the student and is not fully paid
        if ($fee->student_id !== $student->id || $fee->status === 'paid') {
            abort(403, 'Cannot pay this fee');
        }

        return view('student.fees.payment', compact('fee', 'student'));
    }

    public function receipt(Fee $fee)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Ensure fee belongs to the student and is paid
        if ($fee->student_id !== $student->id || $fee->status !== 'paid') {
            abort(403, 'Cannot view receipt for unpaid fee');
        }

        return view('student.fees.receipt', compact('fee', 'student'));
    }

    public function processPayment(Request $request, Fee $fee)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Ensure fee belongs to the student and is not fully paid
        if ($fee->student_id !== $student->id || $fee->status === 'paid') {
            abort(403, 'Cannot pay this fee');
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . ($fee->amount - $fee->paid_amount),
            'payment_method' => 'required|string|max:255',
            'phone_number' => 'required_if:payment_method,bkash,mobile_banking,nagad,rocket|string|max:20',
            'notes' => 'nullable|string|max:1000',
            'terms_accepted' => 'required|accepted',
        ]);

        // Convert mobile_banking to bkash for consistency
        $paymentMethod = $request->payment_method === 'mobile_banking' ? 'bkash' : $request->payment_method;

        // Redirect to our unified payment system with a dummy courseId for fee payments
        // We'll use courseId = 1 as a placeholder since fees don't have courses
        return redirect()->route('student.payments.create', [
            'courseId' => 1, // Dummy course ID for fee payments
            'fee_id' => $fee->id,
            'amount' => $request->amount,
            'payment_method' => $paymentMethod,
            'phone_number' => $request->phone_number,
            'notes' => $request->notes,
        ]);
    }

}