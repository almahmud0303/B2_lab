<x-teacher-layout>
    <x-slot name="header">
        Teacher Dashboard
    </x-slot>

    <!-- Welcome Section -->
    <div class="mb-6 bg-white rounded-lg shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">
            Welcome, {{ $teacher->user->name }}!
        </h1>
        <p class="text-gray-600">
            {{ $teacher->department->name }} | Employee ID: {{ $teacher->employee_id }}
        </p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Total Courses -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">My Courses</p>
                    <p class="text-3xl font-bold mt-2">{{ $courses->count() }}</p>
                </div>
                <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Students -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Total Students</p>
                    <p class="text-3xl font-bold mt-2">{{ $totalStudents }}</p>
                </div>
                <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Upcoming Exams -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Upcoming Exams</p>
                    <p class="text-3xl font-bold mt-2">{{ $upcomingExams->count() }}</p>
                </div>
                <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pending Results -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium">Pending Results</p>
                    <p class="text-3xl font-bold mt-2">{{ $pendingResults->count() }}</p>
                </div>
                <div class="bg-orange-400 bg-opacity-30 rounded-full p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- My Courses -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800">My Courses</h2>
                    <a href="{{ route('teacher.courses.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View All →
                    </a>
                </div>
                <div class="p-6">
                    @if($courseStats->isEmpty())
                        <p class="text-gray-500 text-center py-8">No courses assigned yet.</p>
                    @else
                        <div class="space-y-4">
                            @foreach($courseStats as $stat)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-gray-800">{{ $stat['course']->title }}</h3>
                                            <p class="text-sm text-gray-600 mt-1">{{ $stat['course']->course_code }} | {{ $stat['course']->credits }} Credits</p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $stat['course']->academic_year }} Year, {{ $stat['course']->semester }} Semester
                                            </p>
                                        </div>
                                        <div class="ml-4 text-right">
                                            <p class="text-2xl font-bold text-blue-600">{{ $stat['enrolled_count'] }}</p>
                                            <p class="text-xs text-gray-500">Students</p>
                                        </div>
                                    </div>
                                    <div class="mt-3 flex items-center">
                                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full" 
                                                 style="width: {{ $stat['course']->max_students > 0 ? ($stat['enrolled_count'] / $stat['course']->max_students) * 100 : 0 }}%">
                                            </div>
                                        </div>
                                        <span class="ml-3 text-xs text-gray-600">
                                            {{ $stat['enrolled_count'] }} / {{ $stat['course']->max_students }}
                                        </span>
                                    </div>
                                    <div class="mt-3 flex space-x-2">
                                        <a href="{{ route('teacher.courses.show', $stat['course']->id) }}" 
                                           class="text-xs bg-blue-50 text-blue-600 px-3 py-1 rounded hover:bg-blue-100">
                                            View Details
                                        </a>
                                        <a href="{{ route('teacher.exams.index', ['course_id' => $stat['course']->id]) }}" 
                                           class="text-xs bg-purple-50 text-purple-600 px-3 py-1 rounded hover:bg-purple-100">
                                            Assessments
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Upcoming Exams -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800">Upcoming Exams</h2>
                    <a href="{{ route('teacher.exams.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View All →
                    </a>
                </div>
                <div class="p-6">
                    @if($upcomingExams->isEmpty())
                        <p class="text-gray-500 text-center py-8">No upcoming exams.</p>
                    @else
                        <div class="space-y-3">
                            @foreach($upcomingExams as $exam)
                                <div class="flex items-center justify-between border-l-4 border-purple-500 bg-purple-50 p-4 rounded-r-lg">
                                    <div>
                                        <h3 class="font-semibold text-gray-800">{{ $exam->exam_name }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">{{ $exam->course->title }}</p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            <span class="inline-flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                {{ $exam->exam_date->format('d M, Y') }} at {{ $exam->exam_time }}
                                            </span>
                                        </p>
                                    </div>
                                    <a href="{{ route('teacher.exams.show', $exam->id) }}" 
                                       class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 text-sm">
                                        View
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Recent Notices -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800">Recent Notices</h2>
                    <a href="{{ route('teacher.notices.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View All →
                    </a>
                </div>
                <div class="p-6">
                    @if($recentNotices->isEmpty())
                        <p class="text-gray-500 text-center py-8">No notices available.</p>
                    @else
                        <div class="space-y-3">
                            @foreach($recentNotices as $notice)
                                <div class="border-b border-gray-100 pb-3 last:border-0">
                                    <h3 class="font-medium text-gray-800 text-sm">{{ Str::limit($notice->title, 50) }}</h3>
                                    <p class="text-xs text-gray-500 mt-1">{{ $notice->created_at->diffForHumans() }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Quick Actions</h2>
                </div>
                <div class="p-6 space-y-2">
                    <a href="{{ route('teacher.exams.create') }}" 
                       class="flex items-center p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                        <svg class="w-5 h-5 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-700">Create Assessment</span>
                    </a>
                    
                    <a href="{{ route('teacher.courses.index') }}" 
                       class="flex items-center p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                        <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-700">View My Courses</span>
                    </a>
                    
                    <a href="{{ route('teacher.results.index') }}" 
                       class="flex items-center p-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                        <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-700">Manage Results</span>
                    </a>
                    
                    <a href="{{ route('teacher.profile.index') }}" 
                       class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-700">View Profile</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-teacher-layout>
