<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Hall') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <!-- Header Section -->
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-800">Add New Hall</h3>
                        <a href="{{ route('admin.halls.index') }}" 
                           class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                            Back to Halls
                        </a>
                    </div>

                    <!-- Create Form -->
                    <form action="{{ route('admin.halls.store') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Hall Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Hall Name *</label>
                                <input type="text" name="name" id="name" 
                                       value="{{ old('name') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-300 @enderror" 
                                       required>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Capacity -->
                            <div>
                                <label for="capacity" class="block text-sm font-medium text-gray-700">Capacity *</label>
                                <input type="number" name="capacity" id="capacity" 
                                       value="{{ old('capacity') }}"
                                       min="1" max="1000"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('capacity') border-red-300 @enderror" 
                                       required>
                                @error('capacity')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Location -->
                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                                <input type="text" name="location" id="location" 
                                       value="{{ old('location') }}"
                                       placeholder="e.g., Ground Floor, Building A"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('location') border-red-300 @enderror">
                                @error('location')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status Checkboxes -->
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <input type="checkbox" name="is_available" id="is_available" value="1" 
                                           {{ old('is_available', true) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="is_available" class="ml-2 block text-sm text-gray-900">
                                        Available for Assignment
                                    </label>
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" name="is_active" id="is_active" value="1" 
                                           {{ old('is_active', true) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                        Active
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" id="description" rows="4" 
                                      placeholder="Describe the hall, its features, or any special notes..."
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-300 @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Facilities -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Facilities</label>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @php
                                    $commonFacilities = [
                                        'Air Conditioning', 'Projector', 'Sound System', 'Whiteboard', 
                                        'WiFi', 'Power Outlets', 'Storage', 'Restroom Access',
                                        'Parking', 'Security', 'Maintenance', 'Cleaning Service'
                                    ];
                                @endphp
                                
                                @foreach($commonFacilities as $facility)
                                    <div class="flex items-center">
                                        <input type="checkbox" name="facilities[]" id="facility_{{ $loop->index }}" 
                                               value="{{ $facility }}"
                                               {{ in_array($facility, old('facilities', [])) ? 'checked' : '' }}
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label for="facility_{{ $loop->index }}" class="ml-2 block text-sm text-gray-900">
                                            {{ $facility }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Select all applicable facilities for this hall.</p>
                            @error('facilities')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('admin.halls.index') }}" 
                               class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                                Create Hall
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
