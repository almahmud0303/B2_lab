<x-student-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hall/Hostel Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Current Reservation -->
            @if($currentReservation)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Current Reservation</h3>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="text-lg font-semibold text-green-800">{{ $currentReservation->hall->name }}</h4>
                                    <p class="text-green-600">{{ $currentReservation->hall->department->name ?? 'General' }}</p>
                                    <p class="text-sm text-green-600">Reserved on: {{ $currentReservation->created_at->format('M d, Y') }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ ucfirst($currentReservation->status) }}
                                    </span>
                                    <div class="mt-2">
                                        <a href="{{ route('student.halls.reservation', $currentReservation) }}" 
                                           class="text-green-600 hover:text-green-800 text-sm font-medium">
                                            View Details →
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Available Halls -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Available Halls</h3>
                    
                    @if($halls->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($halls as $hall)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <h4 class="text-lg font-semibold text-gray-900">{{ $hall->name }}</h4>
                                            <p class="text-sm text-gray-600">{{ $hall->department->name ?? 'General' }}</p>
                                        </div>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            Available
                                        </span>
                                    </div>
                                    
                                    <div class="space-y-2 mb-4">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Capacity:</span>
                                            <span class="font-medium">{{ $hall->capacity }} students</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Monthly Fee:</span>
                                            <span class="font-medium">${{ number_format($hall->monthly_fee, 2) }}</span>
                                        </div>
                                    </div>

                                    <!-- Amenities -->
                                    <div class="mb-4">
                                        <p class="text-xs text-gray-600 mb-2">Amenities:</p>
                                        <div class="flex flex-wrap gap-1">
                                            @if($hall->wifi_available)
                                                <span class="inline-flex px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">WiFi</span>
                                            @endif
                                            @if($hall->laundry_available)
                                                <span class="inline-flex px-2 py-1 text-xs bg-purple-100 text-purple-800 rounded">Laundry</span>
                                            @endif
                                            @if($hall->kitchen_available)
                                                <span class="inline-flex px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Kitchen</span>
                                            @endif
                                            @if($hall->parking_available)
                                                <span class="inline-flex px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded">Parking</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex space-x-2">
                                        <a href="{{ route('student.halls.show', $hall) }}" 
                                           class="flex-1 bg-blue-500 hover:bg-blue-700 text-white text-center py-2 px-3 rounded text-sm font-medium">
                                            View Details
                                        </a>
                                        @if(!$currentReservation)
                                            <a href="{{ route('student.halls.show', $hall) }}" 
                                               class="flex-1 bg-green-500 hover:bg-green-700 text-white text-center py-2 px-3 rounded text-sm font-medium">
                                                Reserve
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No halls available</h3>
                            <p class="mt-1 text-sm text-gray-500">There are currently no available halls for reservation.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Reservation History -->
            @if($reservationHistory->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Recent Reservations</h3>
                            <a href="{{ route('student.halls.history') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View All →
                            </a>
                        </div>
                        
                        <div class="space-y-3">
                            @foreach($reservationHistory as $reservation)
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900">{{ $reservation->hall->name }}</h4>
                                        <p class="text-xs text-gray-600">{{ $reservation->created_at->format('M d, Y') }}</p>
                                    </div>
                                    <div class="text-right">
                                        @php
                                            $badgeClass = match($reservation->status) {
                                                'active' => 'bg-green-100 text-green-800',
                                                'approved' => 'bg-blue-100 text-blue-800',
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'cancelled' => 'bg-red-100 text-red-800',
                                                default => 'bg-gray-100 text-gray-800'
                                            };
                                        @endphp
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $badgeClass }}">
                                            {{ ucfirst($reservation->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-student-layout>
