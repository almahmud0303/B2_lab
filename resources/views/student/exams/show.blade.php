<x-student-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $exam->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Exam Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Exam Details</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Exam Title</dt>
                                    <dd class="text-sm text-gray-900">{{ $exam->title }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Course</dt>
                                    <dd class="text-sm text-gray-900">{{ $exam->course->title }} ({{ $exam->course->course_code }})</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Exam Type</dt>
                                    <dd class="text-sm">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ ucfirst($exam->type) }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Total Marks</dt>
                                    <dd class="text-sm text-gray-900">{{ $exam->total_marks }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Duration</dt>
                                    <dd class="text-sm text-gray-900">{{ $exam->duration }} minutes</dd>
                                </div>
                            </dl>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Schedule & Venue</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Exam Date</dt>
                                    <dd class="text-sm text-gray-900">{{ $exam->exam_date->format('l, F d, Y') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Start Time</dt>
                                    <dd class="text-sm text-gray-900">{{ $exam->start_time->format('h:i A') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">End Time</dt>
                                    <dd class="text-sm text-gray-900">{{ $exam->end_time->format('h:i A') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Venue</dt>
                                    <dd class="text-sm text-gray-900">{{ $exam->venue ?? 'To be announced' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="text-sm">
                                        @if($exam->exam_date > now())
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Upcoming
                                            </span>
                                        @else
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                Completed
                                            </span>
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Exam Instructions -->
            @if($exam->instructions)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Exam Instructions</h3>
                    <div class="prose max-w-none">
                        {!! nl2br(e($exam->instructions)) !!}
                    </div>
                </div>
            </div>
            @endif

            <!-- Results Section -->
            @if($exam->exam_date < now() && $result)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Your Result</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ $result->obtained_marks }}</div>
                            <div class="text-sm text-gray-500">Obtained Marks</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-gray-900">{{ $result->total_marks }}</div>
                            <div class="text-sm text-gray-500">Total Marks</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">{{ $result->percentage }}%</div>
                            <div class="text-sm text-gray-500">Percentage</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold {{ $result->grade === 'F' ? 'text-red-600' : 'text-green-600' }}">
                                {{ $result->grade }}
                            </div>
                            <div class="text-sm text-gray-500">Grade</div>
                        </div>
                    </div>
                    
                    @if($result->remarks)
                    <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                        <h4 class="text-sm font-medium text-blue-900">Remarks</h4>
                        <p class="text-sm text-blue-800 mt-1">{{ $result->remarks }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @elseif($exam->exam_date < now())
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Result Pending</h3>
                        <p class="mt-1 text-sm text-gray-500">Your result for this exam is not yet published.</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Exam Guidelines -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Important Guidelines</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Arrive at least 15 minutes before the exam starts</li>
                                <li>Bring your student ID card and exam permit</li>
                                <li>No electronic devices (phones, calculators, etc.) are allowed</li>
                                <li>Use only blue or black ink pens</li>
                                <li>Read all instructions carefully before starting</li>
                                <li>Raise your hand if you have any questions</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center">
                <a href="{{ route('student.exams.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Exams
                </a>
                
                @if($exam->exam_date > now())
                <div class="text-sm text-gray-500">
                    Exam starts in {{ $exam->exam_date->diffForHumans() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-student-layout>
