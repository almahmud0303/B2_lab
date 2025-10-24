<x-student-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Academic Calendar') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('student.calendar.export', ['format' => 'ics']) }}" 
                   class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded text-sm">
                    Export Calendar
                </a>
                <a href="{{ route('student.notices.index') }}" 
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                    View Notices
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Calendar Navigation -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <a href="{{ route('student.calendar.index', ['month' => $currentMonth->copy()->subMonth()->format('Y-m')]) }}" 
                           class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-l">
                            Previous Month
                        </a>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $currentMonth->format('F Y') }}</h3>
                        <a href="{{ route('student.calendar.index', ['month' => $currentMonth->copy()->addMonth()->format('Y-m')]) }}" 
                           class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-r">
                            Next Month
                        </a>
                    </div>

                    <!-- Legend -->
                    <div class="flex flex-wrap gap-4 mb-4">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-red-500 rounded mr-2"></div>
                            <span class="text-sm text-gray-600">Exams</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-orange-500 rounded mr-2"></div>
                            <span class="text-sm text-gray-600">High Priority Notices</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-500 rounded mr-2"></div>
                            <span class="text-sm text-gray-600">Regular Notices</span>
                        </div>
                    </div>

                    <!-- Calendar Grid -->
                    <div class="grid grid-cols-7 gap-px bg-gray-200 rounded-lg overflow-hidden shadow-md">
                        <!-- Days of the week -->
                        @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                            <div class="bg-gray-100 text-center py-2 text-sm font-medium text-gray-700">{{ $day }}</div>
                        @endforeach

                        <!-- Calendar Days -->
                        @foreach($calendarDays as $day)
                            <div class="relative h-32 bg-white p-2 border-b border-r border-gray-200 {{ $day['isCurrentMonth'] ? '' : 'bg-gray-50 text-gray-400' }}">
                                <p class="text-sm font-medium {{ $day['isToday'] ? 'text-blue-600 font-bold bg-blue-100 rounded-full w-6 h-6 flex items-center justify-center' : '' }}">
                                    {{ $day['day'] }}
                                </p>
                                <div class="mt-1 space-y-1 overflow-y-auto max-h-20">
                                    @foreach($day['events'] as $event)
                                        <div class="text-xs bg-{{ $event['color'] }}-100 text-{{ $event['color'] }}-800 rounded-full px-2 py-0.5 truncate" 
                                             title="{{ $event['title'] }}@if(isset($event['course'])) ({{ $event['course'] }})@endif">
                                            {{ $event['title'] }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Upcoming Events -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Upcoming Events (Next 30 Days)</h3>
                    
                    @if($upcomingEvents->count() > 0)
                        <div class="space-y-3">
                            @foreach($upcomingEvents as $event)
                                <div class="flex items-center p-3 bg-{{ $event['color'] }}-50 rounded-lg shadow-sm border-l-4 border-{{ $event['color'] }}-500">
                                    <div class="flex-shrink-0 text-{{ $event['color'] }}-600">
                                        @if($event['type'] === 'exam')
                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                            </svg>
                                        @else
                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $event['title'] }}</p>
                                                @if(isset($event['course']))
                                                    <p class="text-xs text-gray-600">{{ $event['course'] }}</p>
                                                @endif
                                                @if($event['description'])
                                                    <p class="text-xs text-gray-500 mt-1">{{ Str::limit($event['description'], 100) }}</p>
                                                @endif
                                            </div>
                                            <div class="text-right">
                                                <p class="text-xs text-gray-600">{{ \Carbon\Carbon::parse($event['date'])->format('M d, Y') }}</p>
                                                @if(isset($event['time']))
                                                    <p class="text-xs text-gray-600">{{ $event['time'] }}</p>
                                                @endif
                                            </div>
                                        </div>
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
                            <p class="mt-1 text-sm text-gray-500">There are no events scheduled for the next 30 days.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Upcoming Exams</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $upcomingEvents->where('type', 'exam')->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Recent Notices</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $upcomingEvents->where('type', 'notice')->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Events</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $upcomingEvents->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-student-layout>
