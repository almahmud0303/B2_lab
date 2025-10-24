<x-student-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Course Schedule') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Schedule Overview -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Weekly Schedule</h3>
                    
                    @if(count($schedule) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monday</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tuesday</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wednesday</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thursday</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Friday</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($timeSlots as $timeSlot)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $timeSlot }}
                                            </td>
                                            @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday'] as $day)
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    @if(isset($schedule[$day][$timeSlot]))
                                                        <div class="bg-blue-100 p-2 rounded">
                                                            <div class="font-medium">{{ $schedule[$day][$timeSlot]['course'] }}</div>
                                                            <div class="text-xs text-gray-600">{{ $schedule[$day][$timeSlot]['room'] }}</div>
                                                            <div class="text-xs text-gray-500">{{ $schedule[$day][$timeSlot]['teacher'] }}</div>
                                                        </div>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No schedule available</h3>
                            <p class="mt-1 text-sm text-gray-500">Your course schedule will be available once you enroll in courses.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Enrolled Courses Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Enrolled Courses</h3>
                    
                    @if($enrolledCourses->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($enrolledCourses as $enrollment)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <h4 class="font-medium text-gray-900">{{ $enrollment->course->title }}</h4>
                                    <p class="text-sm text-gray-600">{{ $enrollment->course->course_code }}</p>
                                    <p class="text-sm text-gray-500 mt-1">{{ $enrollment->course->teacher->user->name ?? 'Not assigned' }}</p>
                                    <div class="mt-2">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ $enrollment->course->credits }} credits
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">You haven't enrolled in any courses yet.</p>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex justify-center space-x-4">
                <a href="{{ route('student.courses.index') }}" 
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Manage Courses
                </a>
                <a href="{{ route('student.dashboard') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</x-student-layout>
