<x-student-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex space-x-4">
                <a href="{{ route('student.fees.index') }}" 
                   class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium border-b-2 border-transparent hover:border-gray-300 transition-colors">
                    Fee Management
                </a>
                <a href="{{ route('student.payments.history') }}" 
                   class="px-4 py-2 text-blue-600 font-medium border-b-2 border-blue-600">
                    Payment History
                </a>
            </div>
        </div>
    </x-slot>
    
    <div class="py-6">
        <div class="max-w-7xl mx-auto">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Payment History</h2>
                <p class="text-gray-600">View all your course payments</p>
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
                                                @if($payment->payment_method === 'nagad')
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
                    <p class="text-gray-500 mb-6">You haven't made any payments yet</p>
                    <div class="flex justify-center space-x-4">
                        <a href="{{ route('student.courses.index') }}" 
                           class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            Browse Courses
                        </a>
                        <a href="{{ route('student.fees.index') }}" 
                           class="inline-flex items-center px-6 py-3 bg-gray-200 text-gray-800 font-medium rounded-lg hover:bg-gray-300 transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            View Fees
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-student-layout>
