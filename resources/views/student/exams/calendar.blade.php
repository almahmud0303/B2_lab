<x-student-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Academic Calendar') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Calendar Navigation -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $currentMonth->format('F Y') }}
                        </h3>
                        <div class="flex space-x-2">
                            <a href="{{ route('student.exams.calendar', ['month' => $currentMonth->subMonth()->format('Y-m')]) }}" 
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Previous
                            </a>
                            <a href="{{ route('student.exams.calendar') }}" 
                               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Current Month
                            </a>
                            <a href="{{ route('student.exams.calendar', ['month' => $currentMonth->addMonth()->format('Y-m')]) }}" 
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Next
                            </a>
                        </div>
                    </div>

                    <!-- Calendar Grid -->
                    <div class="grid grid-cols-7 gap-1">
                        <!-- Days of the week -->
                        @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                            <div class="p-2 text-center text-sm font-medium text-gray-500 bg-gray-100">
                                {{ $day }}
                            </div>
                        @endforeach

                        <!-- Calendar days -->
                        @foreach($calendarDays as $day)
                            <div class="p-2 min-h-[100px] border border-gray-200 {{ $day['isCurrentMonth'] ? 'bg-white' : 'bg-gray-50' }}">
                                <div class="text-sm font-medium {{ $day['isCurrentMonth'] ? 'text-gray-900' : 'text-gray-400' }}">
                                    {{ $day['day'] }}
                                </div>
                                
                                <!-- Events for this day -->
                                @if(isset($day['events']) && count($day['events']) > 0)
                                    <div class="mt-1 space-y-1">
                                        @foreach($day['events'] as $event)
                                            <div class="text-xs p-1 rounded {{ $event['type'] === 'exam' ? 'bg-red-100 text-red-800' : ($event['type'] === 'assignment' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                                                {{ $event['title'] }}
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Upcoming Events -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Upcoming Events</h3>
                    
                    @if(count($upcomingEvents) > 0)
                        <div class="space-y-3">
                            @foreach($upcomingEvents as $event)
                                <div class="flex items-center justify-between p-3 border rounded-lg {{ $event['type'] === 'exam' ? 'border-red-200 bg-red-50' : ($event['type'] === 'assignment' ? 'border-blue-200 bg-blue-50' : 'border-green-200 bg-green-50') }}">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            @if($event['type'] === 'exam')
                                                <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            @elseif($event['type'] === 'assignment')
                                                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            @else
                                                <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $event['title'] }}</p>
                                            <p class="text-sm text-gray-500">{{ $event['course'] ?? '' }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900">{{ $event['date']->format('M d, Y') }}</p>
                                        <p class="text-xs text-gray-500">{{ $event['date']->format('h:i A') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No upcoming events</h3>
                            <p class="mt-1 text-sm text-gray-500">You don't have any upcoming events scheduled.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Legend -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Legend</h3>
                    <div class="flex space-x-6">
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 bg-red-100 border border-red-200 rounded"></div>
                            <span class="text-sm text-gray-700">Exams</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 bg-blue-100 border border-blue-200 rounded"></div>
                            <span class="text-sm text-gray-700">Assignments</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 bg-green-100 border border-green-200 rounded"></div>
                            <span class="text-sm text-gray-700">Other Events</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="flex justify-center space-x-4">
                <a href="{{ route('student.exams.index') }}" 
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    View Exams
                </a>
                <a href="{{ route('student.exams.results') }}" 
                   class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    View Results
                </a>
                <a href="{{ route('student.dashboard') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</x-student-layout>
