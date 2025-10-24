<x-student-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $course->title }}
            </h2>
            <div class="text-sm text-gray-600">
                {{ $course->course_code }} | {{ $course->credits }} credits
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Course Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Course Details</h3>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Course Code</dt>
                                    <dd class="text-sm text-gray-900">{{ $course->course_code }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Credits</dt>
                                    <dd class="text-sm text-gray-900">{{ $course->credits }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Department</dt>
                                    <dd class="text-sm text-gray-900">{{ $course->department->name ?? 'Not assigned' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Teacher</dt>
                                    <dd class="text-sm text-gray-900">{{ $course->teacher->user->name ?? 'Not assigned' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Semester</dt>
                                    <dd class="text-sm text-gray-900">{{ $course->semester }}</dd>
                                </div>
                            </dl>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Enrollment Information</h3>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Enrollment Date</dt>
                                    <dd class="text-sm text-gray-900">{{ $enrollment->enrollment_date->format('M d, Y') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="text-sm">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ ucfirst($enrollment->status) }}
                                        </span>
                                    </dd>
                                </div>
                                @if($enrollment->grade)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Grade</dt>
                                    <dd class="text-sm text-gray-900">{{ $enrollment->grade }}</dd>
                                </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Course Description -->
            @if($course->description)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Course Description</h3>
                    <p class="text-gray-700">{{ $course->description }}</p>
                </div>
            </div>
            @endif

            <!-- Course Materials -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Course Materials</h3>
                    @if(count($materials) > 0)
                        <div class="space-y-3">
                            @foreach($materials as $material)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $material['title'] }}</p>
                                        <p class="text-sm text-gray-500">{{ $material['type'] }}</p>
                                    </div>
                                    <a href="{{ $material['url'] }}" 
                                       class="bg-blue-500 hover:bg-blue-700 text-white text-sm font-bold py-1 px-3 rounded">
                                        Download
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No course materials available yet.</p>
                    @endif
                </div>
            </div>

            <!-- Assignments -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Assignments</h3>
                    @if(count($assignments) > 0)
                        <div class="space-y-3">
                            @foreach($assignments as $assignment)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $assignment['title'] }}</p>
                                        <p class="text-sm text-gray-500">Due: {{ $assignment['due_date'] }}</p>
                                    </div>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $assignment['status'] === 'submitted' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($assignment['status']) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No assignments available yet.</p>
                    @endif
                </div>
            </div>

            <!-- Course Exams -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Upcoming Exams</h3>
                    @if($course->exams && $course->exams->count() > 0)
                        <div class="space-y-3">
                            @foreach($course->exams->where('exam_date', '>=', now()) as $exam)
                                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $exam->title }}</p>
                                        <p class="text-sm text-gray-500">{{ $exam->exam_date->format('M d, Y') }} at {{ $exam->start_time->format('H:i') }}</p>
                                    </div>
                                    <span class="text-sm text-gray-600">{{ $exam->duration }} minutes</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No upcoming exams for this course.</p>
                    @endif
                </div>
            </div>

            <!-- Course Schedule -->
            @if(count($schedule) > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Course Schedule</h3>
                    <div class="space-y-3">
                        @foreach($schedule as $scheduleItem)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $scheduleItem['day'] }}</p>
                                    <p class="text-sm text-gray-500">{{ $scheduleItem['time'] }}</p>
                                </div>
                                <div class="text-sm text-gray-600">
                                    <p>{{ $scheduleItem['room'] }}</p>
                                    <p>{{ $scheduleItem['type'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Actions -->
            <div class="flex justify-between items-center">
                <a href="{{ route('student.courses.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Courses
                </a>
                
                @if($enrollment->status === 'enrolled')
                <form method="POST" action="{{ route('student.courses.drop', $enrollment) }}" class="inline">
                    @csrf
                    @method('PUT')
                    <button type="submit" 
                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                            onclick="return confirm('Are you sure you want to drop this course?')">
                        Drop Course
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</x-student-layout>
