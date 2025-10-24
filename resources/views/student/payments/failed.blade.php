<x-student-layout>
    <x-slot name="header">Payment Failed</x-slot>
    
    <div class="py-6">
        <div class="max-w-4xl mx-auto">
            <!-- Failed Message -->
            <div class="bg-white p-8 rounded-lg shadow-sm text-center mb-6">
                <div class="mb-6">
                    <svg class="w-24 h-24 mx-auto text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Payment Failed</h2>
                <p class="text-gray-600 text-lg mb-6">Unfortunately, your payment could not be processed</p>
                
                <div class="bg-red-50 border border-red-200 rounded-lg p-6 inline-block">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-red-600 mb-2">
                            {{ $payment->formatted_amount }}
                        </div>
                        <p class="text-sm text-gray-600">Payment ID: {{ $payment->payment_id }}</p>
                        <p class="text-sm text-gray-600">Date: {{ $payment->created_at->format('M d, Y h:i A') }}</p>
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
                            <p><strong>Attempted:</strong> {{ $payment->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Common Reasons -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-bold text-yellow-800 mb-3">💡 Common Reasons for Payment Failure:</h3>
                <ul class="list-disc list-inside space-y-2 text-yellow-700">
                    <li>Insufficient balance in your mobile banking account</li>
                    <li>Incorrect mobile number or PIN entered</li>
                    <li>Network connectivity issues</li>
                    <li>Transaction timeout</li>
                    <li>Bank server maintenance</li>
                    <li>Daily transaction limit exceeded</li>
                </ul>
            </div>

            <!-- What to Do Next -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-bold text-blue-800 mb-3">🔄 What to Do Next:</h3>
                <div class="space-y-2 text-blue-700">
                    <p>1. Check your mobile banking account balance</p>
                    <p>2. Verify your mobile number and PIN</p>
                    <p>3. Try the payment again with a different method</p>
                    <p>4. Contact your bank if the issue persists</p>
                    <p>5. Reach out to our support team for assistance</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('student.payments.create', $payment->course->id) }}" 
                       class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 text-center font-semibold">
                        Try Payment Again
                    </a>
                    <a href="{{ route('student.payments.history') }}" 
                       class="bg-gray-200 text-gray-800 px-8 py-3 rounded-lg hover:bg-gray-300 text-center font-semibold">
                        View Payment History
                    </a>
                    <a href="{{ route('student.courses.index') }}" 
                       class="bg-green-600 text-white px-8 py-3 rounded-lg hover:bg-green-700 text-center font-semibold">
                        Browse Other Courses
                    </a>
                </div>
            </div>

            <!-- Support Information -->
            <div class="bg-white p-6 rounded-lg shadow-sm mt-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Need Help?</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="font-semibold text-gray-700 mb-2">Payment Support</h4>
                        <p class="text-gray-600">Email: payments@university.edu</p>
                        <p class="text-gray-600">Phone: +880-XXX-XXXXXXX</p>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-700 mb-2">Office Hours</h4>
                        <p class="text-gray-600">Sunday to Thursday: 9:00 AM - 5:00 PM</p>
                        <p class="text-gray-600">Friday: Closed</p>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="mt-6 text-center text-gray-500 text-sm">
                <p>Don't worry - you can try the payment again anytime. Your course enrollment is safe.</p>
            </div>
        </div>
    </div>
</x-student-layout>
