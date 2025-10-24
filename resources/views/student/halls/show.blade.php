<x-student-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hall Details: ') }} {{ $hall->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">{{ $hall->name }}</h3>
                            <p class="text-gray-600">{{ $hall->department->name ?? 'General Hall' }}</p>
                        </div>
                        <span class="inline-flex px-3 py-1 text-lg font-semibold rounded-full bg-green-100 text-green-800">
                            Available
                        </span>
                    </div>

                    <!-- Hall Overview -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-3">Basic Information</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Capacity:</span>
                                    <span class="font-medium">{{ $hall->capacity }} students</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Monthly Fee:</span>
                                    <span class="font-medium text-green-600">${{ number_format($hall->monthly_fee, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Location:</span>
                                    <span class="font-medium">{{ $hall->location ?? 'Main Campus' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Contact:</span>
                                    <span class="font-medium">{{ $hall->contact_number ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-3">Amenities</h4>
                            <div class="space-y-2">
                                @foreach($amenities as $amenity => $available)
                                    <div class="flex items-center">
                                        @if($available)
                                            <svg class="h-4 w-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span class="text-green-700">{{ $amenity }}</span>
                                        @else
                                            <svg class="h-4 w-4 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span class="text-gray-500">{{ $amenity }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Hall Description -->
                    @if($hall->description)
                        <div class="mb-8">
                            <h4 class="text-lg font-semibold text-gray-800 mb-3">Description</h4>
                            <p class="text-gray-700 leading-relaxed">{{ $hall->description }}</p>
                        </div>
                    @endif

                    <!-- Rules and Regulations -->
                    @if($hall->rules)
                        <div class="mb-8">
                            <h4 class="text-lg font-semibold text-gray-800 mb-3">Rules & Regulations</h4>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="prose max-w-none text-sm">
                                    {!! nl2br(e($hall->rules)) !!}
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Reservation Form -->
                    @if(!$hasActiveReservation)
                        <div class="border-t border-gray-200 pt-8">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Reserve This Hall</h4>
                            
                            <form method="POST" action="{{ route('student.halls.reserve', $hall) }}" class="space-y-6">
                                @csrf
                                
                                <div>
                                    <label for="room_preference" class="block text-sm font-medium text-gray-700">Room Preference (Optional)</label>
                                    <input type="text" name="room_preference" id="room_preference" 
                                           value="{{ old('room_preference') }}"
                                           placeholder="e.g., Ground floor, Near window, etc."
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('room_preference')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="special_requirements" class="block text-sm font-medium text-gray-700">Special Requirements (Optional)</label>
                                    <textarea name="special_requirements" id="special_requirements" rows="4"
                                              placeholder="Any special requirements or notes..."
                                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('special_requirements') }}</textarea>
                                    @error('special_requirements')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox" name="terms_accepted" id="terms_accepted" required
                                               class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="terms_accepted" class="font-medium text-gray-700">
                                            I agree to the hall rules and regulations
                                        </label>
                                        <p class="text-gray-500">By checking this box, you agree to follow all hall rules and pay the monthly fee of ${{ number_format($hall->monthly_fee, 2) }}.</p>
                                    </div>
                                </div>
                                @error('terms_accepted')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                                <div class="flex justify-end space-x-3">
                                    <a href="{{ route('student.halls.index') }}" 
                                       class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                        Cancel
                                    </a>
                                    <button type="submit" 
                                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        Submit Reservation
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="border-t border-gray-200 pt-8">
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-yellow-800">Already Have Reservation</h3>
                                        <p class="mt-1 text-sm text-yellow-700">
                                            You already have an active hall reservation. Please cancel your current reservation before making a new one.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-student-layout>
