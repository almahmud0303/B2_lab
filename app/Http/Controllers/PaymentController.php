<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Show payment form for a course or fee
     */
    public function create(Request $request, $courseId)
    {
        // Check if this is a fee payment
        if ($request->has('fee_id')) {
            return $this->createFeePayment($request, $courseId);
        }

        $course = Course::findOrFail($courseId);
        
        // Check if user is enrolled in the course
        $studentId = Auth::user()->student?->id;
        if (!$studentId) {
            return redirect()->back()->with('error', 'Student profile not found.');
        }
        
        $enrollment = $course->enrollments()->where('student_id', $studentId)->first();
        if (!$enrollment) {
            return redirect()->back()->with('error', 'You are not enrolled in this course.');
        }

        // Check if payment already exists
        $existingPayment = Payment::where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->where('status', 'completed')
            ->first();

        if ($existingPayment) {
            return redirect()->back()->with('info', 'Payment already completed for this course.');
        }

        return view('student.payments.create', compact('course'));
    }

    /**
     * Create payment for fee (not course)
     */
    private function createFeePayment(Request $request, $courseId)
    {
        $feeId = $request->input('fee_id');
        $fee = \App\Models\Fee::findOrFail($feeId);
        
        $studentId = Auth::user()->student?->id;
        if (!$studentId) {
            return redirect()->back()->with('error', 'Student profile not found.');
        }

        // Ensure fee belongs to the student
        if ($fee->student_id !== $studentId) {
            abort(403, 'Unauthorized access to fee record');
        }

        // Check if already paid
        if ($fee->status === 'paid') {
            return redirect()->route('student.fees.receipt', $fee)
                ->with('info', 'Fee already paid.');
        }

        // Ensure dummy course exists for fee payments
        $this->ensureDummyCourseExists();

        // Pre-fill form data from request
        $formData = [
            'fee' => $fee,
            'amount' => $request->input('amount', $fee->amount - $fee->paid_amount),
            'payment_method' => $request->input('payment_method'),
            'phone_number' => $request->input('phone_number'),
            'notes' => $request->input('notes'),
        ];

        return view('student.payments.create-fee', compact('fee', 'formData', 'courseId'));
    }

    /**
     * Ensure dummy course exists for fee payments
     */
    private function ensureDummyCourseExists()
    {
        $dummyCourse = \App\Models\Course::find(1);
        
        if (!$dummyCourse) {
            \App\Models\Course::create([
                'id' => 1,
                'title' => 'Fee Payment Course (Dummy)',
                'code' => 'FEE001',
                'credits' => 0,
                'description' => 'Dummy course for fee payments',
                'department_id' => 1, // Assuming department 1 exists
                'semester' => '1st',
                'academic_year' => '1st',
                'course_type' => 'optional',
                'max_students' => 0,
                'max_enrollments' => 0,
                'fee_amount' => 0,
                'currency' => 'BDT',
                'fee_required' => false,
                'is_active' => false,
            ]);
        }
    }

    /**
     * Process payment request
     */
    public function store(Request $request, $courseId)
    {
        // Check if this is a fee payment
        if ($request->has('fee_id')) {
            return $this->storeFeePayment($request, $courseId);
        }

        $request->validate([
            'payment_method' => 'required|in:nagad,rocket,bank_transfer,cash',
            'phone_number' => 'required_if:payment_method,nagad,rocket|nullable|string|max:15',
            'notes' => 'nullable|string|max:500',
        ]);

        $course = Course::findOrFail($courseId);
        $student = Auth::user();

        // Check if user is enrolled
        $studentId = $student->student?->id;
        if (!$studentId) {
            return redirect()->back()->with('error', 'Student profile not found.');
        }
        
        $enrollment = $course->enrollments()->where('student_id', $studentId)->first();
        if (!$enrollment) {
            return redirect()->back()->with('error', 'You are not enrolled in this course.');
        }

        // Check if payment already completed
        $existingPayment = Payment::where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->where('status', 'completed')
            ->first();

        if ($existingPayment) {
            return redirect()->back()->with('info', 'Payment already completed for this course.');
        }

        // Generate unique payment ID
        $paymentId = 'PAY_' . Str::upper(Str::random(8)) . '_' . time();

        // Create payment record
        $payment = Payment::create([
            'student_id' => $studentId,
            'course_id' => $course->id,
            'payment_id' => $paymentId,
            'amount' => $course->fee_amount,
            'currency' => $course->currency,
            'payment_method' => $request->payment_method,
            'phone_number' => $request->phone_number,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        // Handle different payment methods
        if (in_array($request->payment_method, ['nagad', 'rocket'])) {
            return $this->processMobilePayment($payment);
        } elseif ($request->payment_method === 'bank_transfer') {
            return $this->processBankTransfer($payment);
        } else {
            return $this->processCashPayment($payment);
        }
    }

    /**
     * Process fee payment
     */
    private function storeFeePayment(Request $request, $courseId)
    {
        $request->validate([
            'fee_id' => 'required|exists:fees,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:nagad,rocket,bank_transfer,cash',
            'phone_number' => 'required_if:payment_method,nagad,rocket|nullable|string|max:15',
            'notes' => 'nullable|string|max:500',
            'terms_accepted' => 'required|accepted',
        ]);

        $fee = \App\Models\Fee::findOrFail($request->fee_id);
        $student = Auth::user();
        $studentId = $student->student?->id;

        if (!$studentId) {
            return redirect()->back()->with('error', 'Student profile not found.');
        }

        // Ensure fee belongs to the student
        if ($fee->student_id !== $studentId) {
            abort(403, 'Unauthorized access to fee record');
        }

        // Check if already paid
        if ($fee->status === 'paid') {
            return redirect()->route('student.fees.receipt', $fee)
                ->with('info', 'Fee already paid.');
        }

        // Generate unique payment ID
        $paymentId = 'FEE_PAY_' . Str::upper(Str::random(8)) . '_' . time();

        // Create payment record for fee
        $payment = Payment::create([
            'student_id' => $studentId,
            'course_id' => $courseId, // Dummy course ID for fees (usually 1)
            'payment_id' => $paymentId,
            'amount' => $request->amount,
            'currency' => 'BDT',
            'payment_method' => $request->payment_method,
            'phone_number' => $request->phone_number,
            'status' => 'pending',
            'notes' => $request->notes . ' | Fee: ' . $fee->fee_type,
            'metadata' => json_encode(['fee_id' => $fee->id, 'fee_type' => $fee->fee_type]),
        ]);

        // Handle different payment methods
        if (in_array($request->payment_method, ['nagad', 'rocket'])) {
            return $this->processMobilePayment($payment);
        } elseif ($request->payment_method === 'bank_transfer') {
            return $this->processBankTransfer($payment);
        } else {
            return $this->processCashPayment($payment);
        }
    }

    /**
     * Process mobile payment (Nagad, Rocket)
     */
    private function processMobilePayment($payment)
    {
        try {
            $payment->update([
                'status' => 'processing',
                'gateway_response' => json_encode([
                    'method' => $payment->payment_method,
                    'phone' => $payment->phone_number,
                    'amount' => $payment->amount,
                    'created_at' => now()->toISOString()
                ])
            ]);
            
            return redirect()->route('student.payments.instructions', $payment->id);
        } catch (\Exception $e) {
            \Log::error('Mobile payment processing error: ' . $e->getMessage());
            $payment->update([
                'status' => 'failed',
                'gateway_response' => json_encode(['error' => $e->getMessage()])
            ]);
            return redirect()->back()->with('error', 'Payment processing failed. Please try again.');
        }
    }

    /**
     * Process bank transfer
     */
    private function processBankTransfer($payment)
    {
        $payment->update(['status' => 'processing']);
        return redirect()->route('student.payments.instructions', $payment->id);
    }

    /**
     * Process cash payment
     */
    private function processCashPayment($payment)
    {
        $payment->update(['status' => 'pending']);
        return redirect()->route('student.payments.instructions', $payment->id);
    }

    /**
     * Payment callback (simplified without bKash)
     */
    public function callback(Request $request)
    {
        try {
            $paymentID = $request->input('paymentID');
            $status = $request->input('status');

            \Log::info('Payment callback received', [
                'paymentID' => $paymentID,
                'status' => $status,
                'all_params' => $request->all()
            ]);

            if (!$paymentID) {
                \Log::error('Payment callback missing paymentID');
                return response()->json(['error' => 'Missing payment ID'], 400);
            }

            $payment = Payment::where('payment_id', $paymentID)->first();
            
            if (!$payment) {
                \Log::error('Payment not found for paymentID: ' . $paymentID);
                return response()->json(['error' => 'Payment not found'], 404);
            }

            if ($status === 'success' && $payment->status === 'processing') {
                $payment->update([
                    'status' => 'completed',
                    'transaction_id' => $paymentID,
                    'paid_at' => now(),
                ]);

                // Update fee status if this is a fee payment
                $this->updateFeeStatus($payment);

                \Log::info('Payment completed successfully', ['payment_id' => $payment->id]);
                return redirect()->route('student.payments.success', $payment->id)
                    ->with('success', 'Payment completed successfully!');
            } elseif ($status === 'cancel') {
                $payment->update(['status' => 'cancelled']);
                \Log::info('Payment cancelled by user', ['payment_id' => $payment->id]);
                return redirect()->route('student.payments.failed', $payment->id)
                    ->with('error', 'Payment was cancelled.');
            } else {
                \Log::warning('Payment callback with unexpected status', [
                    'payment_id' => $payment->id,
                    'status' => $status,
                    'current_status' => $payment->status
                ]);
                return redirect()->route('student.dashboard')
                    ->with('error', 'Payment process incomplete.');
            }
        } catch (\Exception $e) {
            \Log::error('Payment callback error: ' . $e->getMessage());
            return redirect()->route('student.dashboard')
                ->with('error', 'Payment process error occurred.');
        }
    }

    /**
     * Show payment instructions
     */
    public function instructions($paymentId)
    {
        $studentId = Auth::user()->student?->id;
        if (!$studentId) {
            abort(403, 'Student profile not found.');
        }

        $payment = Payment::with(['course', 'student'])
            ->where('student_id', $studentId)
            ->findOrFail($paymentId);

        return view('student.payments.instructions', compact('payment'));
    }

    /**
     * Show payment success page
     */
    public function success($paymentId)
    {
        $studentId = Auth::user()->student?->id;
        if (!$studentId) {
            abort(403, 'Student profile not found.');
        }

        $payment = Payment::with(['course', 'student'])
            ->where('student_id', $studentId)
            ->findOrFail($paymentId);

        return view('student.payments.success', compact('payment'));
    }

    /**
     * Show payment failed page
     */
    public function failed($paymentId)
    {
        $studentId = Auth::user()->student?->id;
        if (!$studentId) {
            abort(403, 'Student profile not found.');
        }

        $payment = Payment::with(['course', 'student'])
            ->where('student_id', $studentId)
            ->findOrFail($paymentId);

        return view('student.payments.failed', compact('payment'));
    }

    /**
     * Show payment history
     */
    public function history()
    {
        $studentId = Auth::user()->student?->id;
        if (!$studentId) {
            abort(403, 'Student profile not found.');
        }

        $payments = Payment::with(['course'])
            ->where('student_id', $studentId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('student.payments.history', compact('payments'));
    }

    /**
     * Show payment details
     */
    public function show($paymentId)
    {
        $studentId = Auth::user()->student?->id;
        if (!$studentId) {
            abort(403, 'Student profile not found.');
        }

        $payment = Payment::with(['course', 'student'])
            ->where('student_id', $studentId)
            ->findOrFail($paymentId);

        return view('student.payments.show', compact('payment'));
    }

    /**
     * Update fee status when payment is completed
     */
    private function updateFeeStatus($payment)
    {
        // Check if this is a fee payment
        $metadata = $payment->metadata ? json_decode($payment->metadata, true) : null;
        
        if ($metadata && isset($metadata['fee_id'])) {
            $fee = \App\Models\Fee::find($metadata['fee_id']);
            
            if ($fee) {
                $newPaidAmount = $fee->paid_amount + $payment->amount;
                $fee->update([
                    'status' => $newPaidAmount >= $fee->amount ? 'paid' : 'partial',
                    'paid_amount' => $newPaidAmount,
                    'paid_date' => now(),
                ]);
                
                \Log::info('Fee status updated', [
                    'fee_id' => $fee->id,
                    'new_status' => $fee->status,
                    'new_paid_amount' => $newPaidAmount
                ]);
            }
        }
    }
}