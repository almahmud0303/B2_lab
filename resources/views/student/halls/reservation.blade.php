<x-student-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reservation Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">Reservation #{{ $reservation->id }}</h3>
                            <p class="text-gray-600">{{ $reservation->hall->name }} - {{ $reservation->hall->department->name ?? 'General' }}</p>
                        </div>
                        @php
                            $badgeClass = match($reservation->status) {
                                'active' => 'bg-green-100 text-green-800',
                                'approved' => 'bg-blue-100 text-blue-800',
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                                'rejected' => 'bg-red-100 text-red-800',
                                default => 'bg-gray-100 text-gray-800'
                            };
                        @endphp
                        <span class="inline-flex px-3 py-1 text-lg font-semibold rounded-full {{ $badgeClass }}">
                            {{ ucfirst($reservation->status) }}
                        </span>
                    </div>

                    <!-- Reservation Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-3">Reservation Details</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Reservation Date:</span>
                                    <span class="font-medium">{{ $reservation->reservation_date->format('M d, Y H:i') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Hall:</span>
                                    <span class="font-medium">{{ $reservation->hall->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Monthly Fee:</span>
                                    <span class="font-medium text-green-600">${{ number_format($reservation->hall->monthly_fee, 2) }}</span>
                                </div>
                                @if($reservation->approved_at)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Approved At:</span>
                                        <span class="font-medium">{{ $reservation->approved_at->format('M d, Y H:i') }}</span>
                                    </div>
                                @endif
                                @if($reservation->cancelled_at)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Cancelled At:</span>
                                        <span class="font-medium">{{ $reservation->cancelled_at->format('M d, Y H:i') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-3">Student Information</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Student ID:</span>
                                    <span class="font-medium">{{ $reservation->student->student_id }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Name:</span>
                                    <span class="font-medium">{{ $reservation->student->user->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Email:</span>
                                    <span class="font-medium">{{ $reservation->student->user->email }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Phone:</span>
                                    <span class="font-medium">{{ $reservation->student->user->phone ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Room Preference -->
                    @if($reservation->room_preference)
                        <div class="mb-8">
                            <h4 class="text-lg font-semibold text-gray-800 mb-3">Room Preference</h4>
                            <p class="text-gray-700 bg-gray-50 rounded-lg p-4">{{ $reservation->room_preference }}</p>
                        </div>
                    @endif

                    <!-- Special Requirements -->
                    @if($reservation->special_requirements)
                        <div class="mb-8">
                            <h4 class="text-lg font-semibold text-gray-800 mb-3">Special Requirements</h4>
                            <p class="text-gray-700 bg-gray-50 rounded-lg p-4">{{ $reservation->special_requirements }}</p>
                        </div>
                    @endif

                    <!-- Admin Notes -->
                    @if($reservation->admin_notes)
                        <div class="mb-8">
                            <h4 class="text-lg font-semibold text-gray-800 mb-3">Admin Notes</h4>
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <p class="text-blue-800">{{ $reservation->admin_notes }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Status Messages -->
                    @if($reservation->status === 'pending')
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                            <div class="flex">
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Reservation Pending</h3>
                                    <p class="mt-1 text-sm text-yellow-700">
                                        Your reservation is currently under review. You will be notified once it's approved or if any additional information is required.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @elseif($reservation->status === 'approved')
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                            <div class="flex">
                                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-green-800">Reservation Approved</h3>
                                    <p class="mt-1 text-sm text-green-700">
                                        Congratulations! Your reservation has been approved. Please contact the hall administration for further instructions.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @elseif($reservation->status === 'rejected')
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                            <div class="flex">
                                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Reservation Rejected</h3>
                                    <p class="mt-1 text-sm text-red-700">
                                        Your reservation has been rejected. Please contact the hall administration for more information or try reserving a different hall.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="flex justify-end space-x-3">
                        @if(in_array($reservation->status, ['pending', 'approved']))
                            <form method="POST" action="{{ route('student.halls.cancel', $reservation) }}" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                                        onclick="return confirm('Are you sure you want to cancel this reservation?')">
                                    Cancel Reservation
                                </button>
                            </form>
                        @endif
                        
                        <a href="{{ route('student.halls.index') }}" 
                           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Back to Halls
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-student-layout>
