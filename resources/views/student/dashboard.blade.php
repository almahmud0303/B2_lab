<x-student-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Student Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                        Welcome back, {{ $student->user->name }}!
                    </h3>
                    <p class="text-gray-600">
                        Student ID: {{ $student->student_id }} | 
                        Department: {{ $student->department->name ?? 'Not assigned' }} | 
                        CGPA: {{ $gpa }}
                    </p>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Enrolled Courses</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $enrollments->count() }}</dd>
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
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
                                <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Pending Fees</dt>
                                    <dd class="text-lg font-medium text-gray-900">${{ number_format($feeStats['total_pending'], 2) }}</dd>
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Borrowed Books</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $activeBookIssues->count() }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Assigned Hall</dt>
                                    <dd class="text-lg font-medium text-gray-900">
                                        {{ $student->hall ? $student->hall->name : 'Not Assigned' }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Enrolled Courses -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Enrolled Courses</h3>
                        <div class="space-y-3">
                            @forelse($enrollments as $enrollment)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $enrollment->course->title }}</p>
                                        <p class="text-sm text-gray-500">{{ $enrollment->course->course_code }} | {{ $enrollment->course->credits }} credits</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-600">{{ $enrollment->course->teacher->user->name ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-500">{{ $enrollment->course->department->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">No enrolled courses found.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Upcoming Exams -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Upcoming Exams</h3>
                        <div class="space-y-3">
                            @forelse($upcomingExams as $exam)
                                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $exam->title }}</p>
                                        <p class="text-sm text-gray-500">{{ $exam->course->title }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-600">{{ $exam->exam_date->format('M d, Y') }}</p>
                                        <p class="text-xs text-gray-500">{{ $exam->start_time->format('H:i') }} - {{ $exam->end_time->format('H:i') }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">No upcoming exams found.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Recent Results -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Results</h3>
                        <div class="space-y-3">
                            @forelse($recentResults as $result)
                                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $result->exam->title }}</p>
                                        <p class="text-sm text-gray-500">{{ $result->exam->course->title }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900">{{ $result->grade ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-500">{{ $result->percentage }}%</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">No recent results found.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Recent Notices -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Notices</h3>
                        <div class="space-y-3">
                            @forelse($recentNotices as $notice)
                                <div class="p-3 bg-yellow-50 rounded-lg">
                                    <p class="font-medium text-gray-900">{{ $notice->title }}</p>
                                    <p class="text-sm text-gray-600 mt-1">{{ Str::limit($notice->content, 100) }}</p>
                                    <p class="text-xs text-gray-500 mt-2">{{ $notice->created_at->diffForHumans() }}</p>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">No recent notices found.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-student-layout>
