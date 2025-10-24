<x-student-layout>
    <x-slot name="header">Payment Successful</x-slot>
    
    <div class="py-6">
        <div class="max-w-4xl mx-auto">
            <!-- Success Message -->
            <div class="bg-white p-8 rounded-lg shadow-sm text-center mb-6">
                <div class="mb-6">
                    <svg class="w-24 h-24 mx-auto text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Payment Successful!</h2>
                <p class="text-gray-600 text-lg mb-6">Your payment has been processed successfully</p>
                
                <div class="bg-green-50 border border-green-200 rounded-lg p-6 inline-block">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600 mb-2">
                            {{ $payment->formatted_amount }}
                        </div>
                        <p class="text-sm text-gray-600">Payment ID: {{ $payment->payment_id }}</p>
                        @if($payment->transaction_id)
                            <p class="text-sm text-gray-600">Transaction ID: {{ $payment->transaction_id }}</p>
                        @endif
                        <p class="text-sm text-gray-600">Date: {{ $payment->paid_at ? $payment->paid_at->format('M d, Y h:i A') : now()->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
            </div>

            <!-- Course Information -->
            <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Course Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-semibold text-gray-700 mb-2">Course Information</h4>
                        <div class="space-y-2 text-gray-600">
                            <p><strong>Title:</strong> {{ $payment->course->title }}</p>
                            <p><strong>Code:</strong> {{ $payment->course->course_code }}</p>
                            <p><strong>Department:</strong> {{ $payment->course->department->name }}</p>
                            <p><strong>Teacher:</strong> {{ $payment->course->teacher->user->name }}</p>
                            <p><strong>Credits:</strong> {{ $payment->course->credits }}</p>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-700 mb-2">Payment Information</h4>
                        <div class="space-y-2 text-gray-600">
                            <p><strong>Method:</strong> {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
                            @if($payment->phone_number)
                                <p><strong>Phone:</strong> {{ $payment->phone_number }}</p>
                            @endif
                            <p><strong>Status:</strong> 
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $payment->status_badge }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </p>
                            <p><strong>Paid At:</strong> {{ $payment->paid_at ? $payment->paid_at->format('M d, Y h:i A') : now()->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-bold text-blue-800 mb-3">🎉 What's Next?</h3>
                <div class="space-y-2 text-blue-700">
                    <p>✅ Your course enrollment is now complete</p>
                    <p>📧 You'll receive a confirmation email shortly</p>
                    <p>📚 You can now access course materials and attend classes</p>
                    <p>📊 Check your course schedule in the student dashboard</p>
                    <p>💬 Contact your teacher if you have any questions</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('student.courses.index') }}" 
                       class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 text-center font-semibold">
                        View My Courses
                    </a>
                    <a href="{{ route('student.payments.show', $payment->id) }}?receipt=1" 
                       class="bg-green-600 text-white px-8 py-3 rounded-lg hover:bg-green-700 text-center font-semibold">
                        Download Receipt
                    </a>
                    <a href="{{ route('student.dashboard') }}" 
                       class="bg-gray-200 text-gray-800 px-8 py-3 rounded-lg hover:bg-gray-300 text-center font-semibold">
                        Go to Dashboard
                    </a>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="mt-6 text-center text-gray-500 text-sm">
                <p>If you have any questions about this payment, please contact our support team.</p>
                <p>Email: payments@university.edu | Phone: +880-XXX-XXXXXXX</p>
            </div>
        </div>
    </div>
</x-student-layout>
