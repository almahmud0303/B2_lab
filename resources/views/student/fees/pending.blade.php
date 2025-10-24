<x-student-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pending Fees') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Pending Fees</h3>
                    
                    @if($pendingFees->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($pendingFees as $fee)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <h4 class="text-lg font-semibold text-gray-900">{{ ucfirst($fee->fee_type) }}</h4>
                                            <p class="text-sm text-gray-600">{{ $fee->created_at->format('M d, Y') }}</p>
                                        </div>
                                        @php
                                            $badgeClass = match($fee->status) {
                                                'overdue' => 'bg-red-100 text-red-800',
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'partial' => 'bg-blue-100 text-blue-800',
                                                default => 'bg-gray-100 text-gray-800'
                                            };
                                        @endphp
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $badgeClass }}">
                                            {{ ucfirst($fee->status) }}
                                        </span>
                                    </div>
                                    
                                    <div class="space-y-2 mb-4">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Amount:</span>
                                            <span class="font-medium">${{ number_format($fee->amount, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Due Date:</span>
                                            <span class="font-medium">{{ $fee->due_date->format('M d, Y') }}</span>
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
                                           class="flex-1 bg-green-500 hover:bg-green-700 text-white text-center py-2 px-3 rounded text-sm font-medium">
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
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No pending fees</h3>
                            <p class="mt-1 text-sm text-gray-500">You're all caught up! No fees to pay.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-student-layout>
