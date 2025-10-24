<x-student-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Overdue Fees') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                        <div class="flex">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Overdue Fees Notice</h3>
                                <p class="mt-1 text-sm text-red-700">
                                    Please pay your overdue fees as soon as possible to avoid any additional penalties or restrictions.
                                </p>
                            </div>
                        </div>
                    </div>

                    <h3 class="text-lg font-medium text-gray-900 mb-4">Overdue Fees</h3>
                    
                    @if($overdueFees->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($overdueFees as $fee)
                                <div class="border border-red-200 rounded-lg p-4 bg-red-50">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <h4 class="text-lg font-semibold text-gray-900">{{ ucfirst($fee->fee_type) }}</h4>
                                            <p class="text-sm text-gray-600">{{ $fee->created_at->format('M d, Y') }}</p>
                                        </div>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            Overdue
                                        </span>
                                    </div>
                                    
                                    <div class="space-y-2 mb-4">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Amount:</span>
                                            <span class="font-medium text-red-600">${{ number_format($fee->amount, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Due Date:</span>
                                            <span class="font-medium text-red-600">{{ $fee->due_date->format('M d, Y') }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Days Overdue:</span>
                                            <span class="font-medium text-red-600">{{ $fee->due_date->diffInDays(now()) }} days</span>
                                        </div>
                                        @if($fee->status === 'partial')
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-600">Paid:</span>
                                                <span class="font-medium">${{ number_format($fee->paid_amount, 2) }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex space-x-2">
                                        <a href="{{ route('student.fees.show', $fee) }}" 
                                           class="flex-1 bg-blue-500 hover:bg-blue-700 text-white text-center py-2 px-3 rounded text-sm font-medium">
                                            View Details
                                        </a>
                                        <a href="{{ route('student.fees.payment', $fee) }}" 
                                           class="flex-1 bg-red-500 hover:bg-red-700 text-white text-center py-2 px-3 rounded text-sm font-medium">
                                            Pay Now
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No overdue fees</h3>
                            <p class="mt-1 text-sm text-gray-500">Great! You don't have any overdue fees.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-student-layout>
