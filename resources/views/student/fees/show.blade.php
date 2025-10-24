<x-student-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Fee Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Fee Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Fee Details</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Fee Type</dt>
                                    <dd class="text-sm text-gray-900">{{ ucfirst($fee->fee_type) }} Fee</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Amount</dt>
                                    <dd class="text-sm text-gray-900">${{ number_format($fee->amount, 2) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Due Date</dt>
                                    <dd class="text-sm text-gray-900">{{ $fee->due_date->format('M d, Y') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="text-sm">
                                        @php
                                            $badgeClass = match($fee->status) {
                                                'paid' => 'bg-green-100 text-green-800',
                                                'partial' => 'bg-yellow-100 text-yellow-800',
                                                'pending' => 'bg-blue-100 text-blue-800',
                                                'overdue' => 'bg-red-100 text-red-800',
                                                default => 'bg-gray-100 text-gray-800'
                                            };
                                        @endphp
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $badgeClass }}">
                                            {{ ucfirst($fee->status) }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Information</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Paid Amount</dt>
                                    <dd class="text-sm text-gray-900">${{ number_format($fee->paid_amount, 2) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Remaining Amount</dt>
                                    <dd class="text-sm text-gray-900">${{ number_format($fee->amount - $fee->paid_amount, 2) }}</dd>
                                </div>
                                @if($fee->paid_date)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Paid Date</dt>
                                    <dd class="text-sm text-gray-900">{{ $fee->paid_date->format('M d, Y') }}</dd>
                                </div>
                                @endif
                                @if($fee->payment_method)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Payment Method</dt>
                                    <dd class="text-sm text-gray-900">{{ ucfirst($fee->payment_method) }}</dd>
                                </div>
                                @endif
                            </dl>
                        </div>
                    </div>

                    @if($fee->notes)
                    <div class="mt-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Notes</h4>
                        <p class="text-sm text-gray-700">{{ $fee->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Payment History -->
            @if($fee->status === 'paid' || $fee->status === 'partial')
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Payment History</h3>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-green-900">Payment of ${{ number_format($fee->paid_amount, 2) }}</p>
                                <p class="text-sm text-green-700">{{ $fee->paid_date ? $fee->paid_date->format('M d, Y \a\t h:i A') : 'Date not available' }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ $fee->payment_method ? ucfirst($fee->payment_method) : 'Payment' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Payment Actions -->
            @if($fee->status !== 'paid')
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Make Payment</h3>
                    <p class="text-sm text-gray-600 mb-4">
                        @if($fee->status === 'partial')
                            Remaining amount: ${{ number_format($fee->amount - $fee->paid_amount, 2) }}
                        @else
                            Total amount: ${{ number_format($fee->amount, 2) }}
                        @endif
                    </p>
                    <a href="{{ route('student.fees.payment', $fee) }}" 
                       class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Pay Now
                    </a>
                </div>
            </div>
            @endif

            <!-- Payment Guidelines -->
            <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Payment Guidelines</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Payments are processed securely through our payment gateway</li>
                                <li>You can pay using credit/debit cards or bank transfer</li>
                                <li>Payment receipts will be available for download after successful payment</li>
                                <li>Contact the finance office if you need assistance with payment</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center">
                <a href="{{ route('student.fees.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Fees
                </a>
                
                @if($fee->paid_amount > 0)
                <a href="{{ route('student.fees.receipt', $fee) }}" 
                   class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                    Download Receipt
                </a>
                @endif
            </div>
        </div>
    </div>
</x-student-layout>
