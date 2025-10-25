<x-student-layout>
    <x-slot name="header">Payment Details</x-slot>
    
    <div class="py-6">
        <div class="max-w-4xl mx-auto">
            <!-- Payment Header -->
            <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Payment Details</h2>
                        <p class="text-gray-600">Payment ID: {{ $payment->payment_id }}</p>
                        @if($payment->transaction_id)
                            <p class="text-gray-600">Transaction ID: {{ $payment->transaction_id }}</p>
                        @endif
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold text-green-600">
                            {{ $payment->formatted_amount }}
                        </div>
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $payment->status_badge }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Course Information -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Course Information</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Course Title</label>
                            <p class="text-gray-900 font-semibold">{{ $payment->course->title }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Course Code</label>
                            <p class="text-gray-900">{{ $payment->course->course_code }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Department</label>
                            <p class="text-gray-900">{{ $payment->course->department->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Teacher</label>
                            <p class="text-gray-900">{{ $payment->course->teacher->user->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Credits</label>
                            <p class="text-gray-900">{{ $payment->course->credits }}</p>
                        </div>
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Payment Information</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Payment Method</label>
                            <div class="flex items-center">
                                @if($payment->payment_method === 'nagad')
                                    <img src="https://via.placeholder.com/20x20/E31837/FFFFFF?text=NG" alt="Nagad" class="w-5 h-5 rounded mr-2">
                                @elseif($payment->payment_method === 'rocket')
                                    <img src="https://via.placeholder.com/20x20/FF6B35/FFFFFF?text=RT" alt="Rocket" class="w-5 h-5 rounded mr-2">
                                @elseif($payment->payment_method === 'bank_transfer')
                                    <img src="https://via.placeholder.com/20x20/1E40AF/FFFFFF?text=BT" alt="Bank Transfer" class="w-5 h-5 rounded mr-2">
                                @else
                                    <img src="https://via.placeholder.com/20x20/059669/FFFFFF?text=C$" alt="Cash" class="w-5 h-5 rounded mr-2">
                                @endif
                                <span class="text-gray-900 capitalize">{{ str_replace('_', ' ', $payment->payment_method) }}</span>
                            </div>
                        </div>
                        @if($payment->phone_number)
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Phone Number</label>
                                <p class="text-gray-900">{{ $payment->phone_number }}</p>
                            </div>
                        @endif
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Amount</label>
                            <p class="text-gray-900 font-semibold">{{ $payment->formatted_amount }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status</label>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $payment->status_badge }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Created At</label>
                            <p class="text-gray-900">{{ $payment->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        @if($payment->paid_at)
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Paid At</label>
                                <p class="text-gray-900">{{ $payment->paid_at->format('M d, Y h:i A') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            @if($payment->notes)
                <div class="bg-white p-6 rounded-lg shadow-sm mt-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Notes</h3>
                    <p class="text-gray-700">{{ $payment->notes }}</p>
                </div>
            @endif

            <!-- Receipt Section (if payment is completed) -->
            @if($payment->status === 'completed' && request()->get('receipt'))
                <div class="bg-white p-6 rounded-lg shadow-sm mt-6" id="receipt">
                    <div class="text-center">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Payment Receipt</h3>
                        <div class="border-2 border-dashed border-gray-300 p-8 rounded-lg">
                            <div class="text-center">
                                <svg class="w-16 h-16 mx-auto text-green-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h4 class="text-xl font-bold text-gray-800 mb-2">Payment Successful</h4>
                                <p class="text-gray-600 mb-4">Your payment has been processed successfully</p>
                                <div class="bg-gray-50 p-4 rounded-lg inline-block">
                                    <p class="text-sm text-gray-500">Receipt ID: {{ $payment->payment_id }}</p>
                                    <p class="text-sm text-gray-500">Date: {{ $payment->paid_at ? $payment->paid_at->format('M d, Y h:i A') : $payment->created_at->format('M d, Y h:i A') }}</p>
                                    <p class="text-sm text-gray-500">Amount: {{ $payment->formatted_amount }}</p>
                                    <p class="text-sm text-gray-500">Course: {{ $payment->course->title }}</p>
                                    <p class="text-sm text-gray-500">Method: {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 no-print">
                            <button onclick="printReceipt()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 mr-3">
                                Print Receipt
                            </button>
                            <a href="{{ route('student.payments.show', $payment->id) }}" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300">
                                Back to Details
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="bg-white p-6 rounded-lg shadow-sm mt-6">
                <div class="flex justify-between items-center">
                    <a href="{{ route('student.payments.history') }}" 
                       class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300">
                        Back to History
                    </a>
                    
                    @if($payment->status === 'completed')
                        <a href="{{ route('student.payments.show', $payment->id) }}?receipt=1" 
                           class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
                            View Receipt
                        </a>
                    @elseif($payment->status === 'pending')
                        <a href="{{ route('student.payments.instructions', $payment->id) }}" 
                           class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                            View Instructions
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(request()->get('receipt'))
        <style>
            @media print {
                .no-print { display: none !important; }
                body { margin: 0; }
                .container { max-width: none !important; }
            }
        </style>
        <script>
            function printReceipt() {
                window.print();
            }
        </script>
    @endif
</x-student-layout>
