<x-student-layout>
    <x-slot name="header">Payment History</x-slot>
    
    <div class="py-6">
        <div class="max-w-7xl mx-auto">
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Payment History</h2>
                    <p class="text-gray-600">View all your course payments</p>
                </div>
            </div>

            @if($payments->count() > 0)
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Payment Details
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Course
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Amount
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Method
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($payments as $payment)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $payment->payment_id }}
                                                </div>
                                                @if($payment->transaction_id)
                                                    <div class="text-sm text-gray-500">
                                                        Txn: {{ $payment->transaction_id }}
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $payment->course->title }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $payment->course->course_code }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $payment->formatted_amount }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @if($payment->payment_method === 'bkash')
                                                    <img src="https://via.placeholder.com/20x20/00A651/FFFFFF?text=bK" alt="bKash" class="w-5 h-5 rounded mr-2">
                                                @elseif($payment->payment_method === 'nagad')
                                                    <img src="https://via.placeholder.com/20x20/E31837/FFFFFF?text=NG" alt="Nagad" class="w-5 h-5 rounded mr-2">
                                                @elseif($payment->payment_method === 'rocket')
                                                    <img src="https://via.placeholder.com/20x20/FF6B35/FFFFFF?text=RT" alt="Rocket" class="w-5 h-5 rounded mr-2">
                                                @elseif($payment->payment_method === 'bank_transfer')
                                                    <img src="https://via.placeholder.com/20x20/1E40AF/FFFFFF?text=BT" alt="Bank Transfer" class="w-5 h-5 rounded mr-2">
                                                @else
                                                    <img src="https://via.placeholder.com/20x20/059669/FFFFFF?text=C$" alt="Cash" class="w-5 h-5 rounded mr-2">
                                                @endif
                                                <span class="text-sm text-gray-900 capitalize">
                                                    {{ str_replace('_', ' ', $payment->payment_method) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $payment->status_badge }}">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div>
                                                <div>{{ $payment->created_at->format('M d, Y') }}</div>
                                                <div>{{ $payment->created_at->format('h:i A') }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('student.payments.show', $payment->id) }}" 
                                               class="text-blue-600 hover:text-blue-900 mr-3">
                                                View
                                            </a>
                                            @if($payment->status === 'completed')
                                                <a href="{{ route('student.payments.show', $payment->id) }}?receipt=1" 
                                                   class="text-green-600 hover:text-green-900">
                                                    Receipt
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $payments->links() }}
                </div>
            @else
                <div class="bg-white p-12 rounded-lg shadow-sm text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">No Payments Yet</h3>
                    <p class="text-gray-500 mb-4">You haven't made any payments yet</p>
                    <a href="{{ route('student.courses.index') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                        Browse Courses
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-student-layout>
