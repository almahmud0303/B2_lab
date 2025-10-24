<x-student-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Exam Management') }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('student.exams.results') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    View Results
                </a>
                <a href="{{ route('student.exams.calendar') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Calendar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Exam Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Upcoming Exams</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $upcomingExams->count() }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Completed Exams</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $pastExams->count() }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Results Published</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $pastExams->where('results', '!=', null)->count() }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Exams -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Upcoming Exams</h3>
                    
                    @if($upcomingExams->count() > 0)
                        <div class="space-y-4">
                            @foreach($upcomingExams as $exam)
                                <div class="border border-blue-200 rounded-lg p-4 bg-blue-50">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3">
                                                <h4 class="font-medium text-gray-900">{{ $exam->title }}</h4>
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    {{ ucfirst($exam->type) }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-600 mt-1">{{ $exam->course->title }} - {{ $exam->course->department->name ?? 'No Department' }}</p>
                                            <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                                                <span>📅 {{ $exam->exam_date->format('M d, Y') }}</span>
                                                <span>🕐 {{ $exam->start_time->format('H:i') }} - {{ $exam->end_time->format('H:i') }}</span>
                                                <span>📍 {{ $exam->venue ?? 'TBA' }}</span>
                                                <span>📝 {{ $exam->total_marks }} marks</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <a href="{{ route('student.exams.show', $exam) }}" 
                                               class="bg-blue-500 hover:bg-blue-700 text-white text-sm font-bold py-2 px-4 rounded">
                                                View Details
                                            </a>
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
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No upcoming exams</h3>
                            <p class="mt-1 text-sm text-gray-500">You don't have any upcoming exams scheduled.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Past Exams -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Past Exams & Results</h3>
                    
                    @if($pastExams->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marks</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($pastExams as $exam)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center space-x-2">
                                                    <span class="font-medium text-gray-900">{{ $exam->title }}</span>
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                        {{ ucfirst($exam->type) }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $exam->course->title }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $exam->exam_date->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $exam->total_marks }} marks
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($exam->results && $exam->results->count() > 0)
                                                    @php
                                                        $result = $exam->results->first();
                                                        $badgeClass = match($result->grade) {
                                                            'A+', 'A', 'A-' => 'bg-green-100 text-green-800',
                                                            'B+', 'B', 'B-' => 'bg-blue-100 text-blue-800',
                                                            'C+', 'C', 'C-' => 'bg-yellow-100 text-yellow-800',
                                                            'D+', 'D' => 'bg-orange-100 text-orange-800',
                                                            'F' => 'bg-red-100 text-red-800',
                                                            default => 'bg-gray-100 text-gray-800'
                                                        };
                                                    @endphp
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $badgeClass }}">
                                                        {{ $result->grade }}
                                                    </span>
                                                @else
                                                    <span class="text-sm text-gray-500">Pending</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('student.exams.show', $exam) }}" 
                                                   class="text-blue-600 hover:text-blue-900">View</a>
                                                @if($exam->results && $exam->results->count() > 0)
                                                    <a href="{{ route('student.exams.grade-sheet') }}" 
                                                       class="ml-3 text-green-600 hover:text-green-900">Grade Sheet</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No past exams</h3>
                            <p class="mt-1 text-sm text-gray-500">You haven't taken any exams yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Exam Guidelines -->
            <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Exam Guidelines</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Arrive 15 minutes before the exam starts</li>
                                <li>Bring your student ID card</li>
                                <li>No electronic devices allowed</li>
                                <li>Use only blue or black ink pens</li>
                                <li>Read all instructions carefully before starting</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-6 flex justify-center space-x-4">
                <a href="{{ route('student.dashboard') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</x-student-layout>
