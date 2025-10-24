<x-student-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Payment Receipt') }}
            </h2>
            <a href="{{ route('student.fees.receipt-download', $fee) }}" 
               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Download PDF
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Receipt Header -->
                    <div class="text-center mb-8">
                        <h1 class="text-3xl font-bold text-gray-900">Payment Receipt</h1>
                        <p class="text-gray-600">University Management System</p>
                        <p class="text-sm text-gray-500">Receipt #{{ $fee->id }}</p>
                    </div>

                    <!-- Student Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-3">Student Information</h3>
                            <div class="space-y-2">
                                <p><span class="font-medium">Name:</span> {{ $student->user->name }}</p>
                                <p><span class="font-medium">Student ID:</span> {{ $student->student_id }}</p>
                                <p><span class="font-medium">Email:</span> {{ $student->user->email }}</p>
                                <p><span class="font-medium">Department:</span> {{ $student->department->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-3">Payment Details</h3>
                            <div class="space-y-2">
                                <p><span class="font-medium">Receipt Date:</span> {{ now()->format('M d, Y') }}</p>
                                <p><span class="font-medium">Payment Date:</span> {{ $fee->paid_date->format('M d, Y') }}</p>
                                <p><span class="font-medium">Payment Method:</span> {{ $fee->payment_method ?? 'N/A' }}</p>
                                @if($fee->transaction_reference)
                                    <p><span class="font-medium">Transaction ID:</span> {{ $fee->transaction_reference }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Fee Details -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Fee Details</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paid Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ ucfirst($fee->fee_type) }} Fee
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $fee->due_date->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            ${{ number_format($fee->amount, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            ${{ number_format($fee->paid_amount, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $badgeClass = match($fee->status) {
                                                    'paid' => 'bg-green-100 text-green-800',
                                                    'partial' => 'bg-yellow-100 text-yellow-800',
                                                    default => 'bg-gray-100 text-gray-800'
                                                };
                                            @endphp
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $badgeClass }}">
                                                {{ ucfirst($fee->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Payment Summary -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Payment Summary</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Fee Amount:</span>
                                <span class="font-medium">${{ number_format($fee->amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Amount Paid:</span>
                                <span class="font-medium text-green-600">${{ number_format($fee->paid_amount, 2) }}</span>
                            </div>
                            @if($fee->amount > $fee->paid_amount)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Remaining Balance:</span>
                                    <span class="font-medium text-red-600">${{ number_format($fee->amount - $fee->paid_amount, 2) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Notes -->
                    @if($fee->notes)
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-800 mb-3">Notes</h3>
                            <p class="text-gray-700">{{ $fee->notes }}</p>
                        </div>
                    @endif

                    <!-- Footer -->
                    <div class="text-center text-sm text-gray-500 border-t border-gray-200 pt-6">
                        <p>This is an official receipt for your records.</p>
                        <p>For any questions regarding this payment, please contact the finance office.</p>
                        <p class="mt-2">Generated on {{ now()->format('M d, Y \a\t H:i:s') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-student-layout>