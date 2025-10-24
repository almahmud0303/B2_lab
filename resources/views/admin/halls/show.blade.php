<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hall Details: ' . $hall->name) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Hall Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-2xl font-semibold text-gray-800">{{ $hall->name }}</h3>
                            <p class="text-gray-600">{{ $hall->location ?? 'Location not specified' }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.halls.edit', $hall) }}" 
                               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                                Edit Hall
                            </a>
                            <a href="{{ route('admin.halls.index') }}" 
                               class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                                Back to Halls
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Capacity Info -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-gray-800 mb-2">Capacity</h4>
                            <p class="text-2xl font-bold text-blue-600">{{ $hall->assigned_students_count }}/{{ $hall->capacity }}</p>
                            <p class="text-sm text-gray-600">{{ $hall->available_capacity }} spots available</p>
                        </div>

                        <!-- Status -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-gray-800 mb-2">Status</h4>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                @if(!$hall->is_active) bg-gray-100 text-gray-800
                                @elseif($hall->is_available) bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ $hall->status }}
                            </span>
                        </div>

                        <!-- Utilization -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-gray-800 mb-2">Utilization</h4>
                            @php
                                $utilization = $hall->capacity > 0 ? round(($hall->assigned_students_count / $hall->capacity) * 100) : 0;
                            @endphp
                            <p class="text-2xl font-bold text-{{ $utilization > 80 ? 'red' : ($utilization > 60 ? 'yellow' : 'green') }}-600">{{ $utilization }}%</p>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                <div class="bg-{{ $utilization > 80 ? 'red' : ($utilization > 60 ? 'yellow' : 'green') }}-600 h-2 rounded-full" 
                                     style="width: {{ $utilization }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    @if($hall->description)
                        <div class="mt-6">
                            <h4 class="font-semibold text-gray-800 mb-2">Description</h4>
                            <p class="text-gray-700">{{ $hall->description }}</p>
                        </div>
                    @endif

                    <!-- Facilities -->
                    @php
                        $facilities = is_array($hall->facilities) ? $hall->facilities : json_decode($hall->facilities, true);
                        $facilities = $facilities ?: [];
                    @endphp
                    @if(count($facilities) > 0)
                        <div class="mt-6">
                            <h4 class="font-semibold text-gray-800 mb-2">Facilities</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($facilities as $facility)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        {{ $facility }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Student Assignment Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Assigned Students -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Assigned Students ({{ $hall->students->count() }})</h4>
                        
                        @if($hall->students->count() > 0)
                            <div class="space-y-3">
                                @foreach($hall->students as $student)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $student->user->name }}</p>
                                            <p class="text-sm text-gray-600">ID: {{ $student->student_id }} | {{ $student->user->email }}</p>
                                        </div>
                                        <form action="{{ route('admin.halls.remove-student', $student) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-900 text-sm font-medium"
                                                    onclick="return confirm('Are you sure you want to remove this student from the hall?')">
                                                Remove
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <p>No students assigned to this hall yet.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Assign Students -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Assign Students</h4>
                        
                        @if($availableStudents->count() > 0 && $hall->is_active && $hall->is_available)
                            <form action="{{ route('admin.halls.assign-student', $hall) }}" method="POST">
                                @csrf
                                <div class="space-y-3">
                                    @foreach($availableStudents as $student)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $student->user->name }}</p>
                                                <p class="text-sm text-gray-600">ID: {{ $student->student_id }} | {{ $student->user->email }}</p>
                                            </div>
                                            <button type="submit" name="student_id" value="{{ $student->id }}"
                                                    class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">
                                                Assign
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </form>
                        @elseif($availableStudents->count() == 0)
                            <div class="text-center py-8 text-gray-500">
                                <p>All students are already assigned to halls.</p>
                            </div>
                        @elseif(!$hall->is_active)
                            <div class="text-center py-8 text-red-500">
                                <p>Cannot assign students to inactive hall.</p>
                            </div>
                        @elseif(!$hall->is_available)
                            <div class="text-center py-8 text-orange-500">
                                <p>Hall is not available for new assignments.</p>
                            </div>
                        @elseif($hall->assigned_students_count >= $hall->capacity)
                            <div class="text-center py-8 text-red-500">
                                <p>Hall has reached maximum capacity.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
