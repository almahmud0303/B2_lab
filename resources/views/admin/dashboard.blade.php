<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                        Welcome back, {{ Auth::user()->name }}!
                    </h3>
                    <p class="text-gray-600">
                        Manage your university system efficiently with the tools below.
                    </p>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
                <a href="{{ route('admin.students.index') }}" class="block bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Students</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $totalStudents }}</dd>
                                </dl>
                            </div>
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.teachers.index') }}" class="block bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Teachers</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $totalTeachers }}</dd>
                                </dl>
                            </div>
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.departments.index') }}" class="block bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Departments</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $totalDepartments }}</dd>
                                </dl>
                            </div>
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.courses.index') }}" class="block bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Courses</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $totalCourses }}</dd>
                                </dl>
                            </div>
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.halls.index') }}" class="block bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Halls</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $totalHalls }}</dd>
                                </dl>
                            </div>
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Management Icons Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <!-- Students Management -->
                <a href="{{ route('admin.students.index') }}" class="group bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-all duration-300">
                    <div class="p-6 text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 group-hover:bg-blue-200 transition-colors">
                            <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Students</h3>
                        <p class="mt-2 text-sm text-gray-500">Manage student records, enrollments, and academic progress</p>
                        <div class="mt-4 text-blue-600 font-medium">View Details →</div>
                    </div>
                </a>

                <!-- Teachers Management -->
                <a href="{{ route('admin.teachers.index') }}" class="group bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-all duration-300">
                    <div class="p-6 text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 group-hover:bg-green-200 transition-colors">
                            <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Teachers</h3>
                        <p class="mt-2 text-sm text-gray-500">Manage teacher profiles, courses, and academic assignments</p>
                        <div class="mt-4 text-green-600 font-medium">View Details →</div>
                    </div>
                </a>

                <!-- Departments Management -->
                <a href="{{ route('admin.departments.index') }}" class="group bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-all duration-300">
                    <div class="p-6 text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-purple-100 group-hover:bg-purple-200 transition-colors">
                            <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Departments</h3>
                        <p class="mt-2 text-sm text-gray-500">Manage academic departments and their structures</p>
                        <div class="mt-4 text-purple-600 font-medium">View Details →</div>
                    </div>
                </a>

                <!-- Courses Management -->
                <a href="{{ route('admin.courses.index') }}" class="group bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-all duration-300">
                    <div class="p-6 text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 group-hover:bg-yellow-200 transition-colors">
                            <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Courses</h3>
                        <p class="mt-2 text-sm text-gray-500">Manage course catalog, schedules, and curriculum</p>
                        <div class="mt-4 text-yellow-600 font-medium">View Details →</div>
                    </div>
                </a>

                <!-- Exams Management -->
                <a href="{{ route('admin.exams.index') }}" class="group bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-all duration-300">
                    <div class="p-6 text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 group-hover:bg-red-200 transition-colors">
                            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Exams</h3>
                        <p class="mt-2 text-sm text-gray-500">Schedule and manage examinations and assessments</p>
                        <div class="mt-4 text-red-600 font-medium">View Details →</div>
                    </div>
                </a>

                <!-- Results Management -->
                <a href="{{ route('admin.results.index') }}" class="group bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-all duration-300">
                    <div class="p-6 text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-indigo-100 group-hover:bg-indigo-200 transition-colors">
                            <svg class="h-8 w-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Results</h3>
                        <p class="mt-2 text-sm text-gray-500">View and manage student examination results</p>
                        <div class="mt-4 text-indigo-600 font-medium">View Details →</div>
                    </div>
                </a>

                <!-- Fees Management -->
                <a href="{{ route('admin.fees.index') }}" class="group bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-all duration-300">
                    <div class="p-6 text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 group-hover:bg-green-200 transition-colors">
                            <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Fees</h3>
                        <p class="mt-2 text-sm text-gray-500">Manage student fees, payments, and financial records</p>
                        <div class="mt-4 text-green-600 font-medium">View Details →</div>
                    </div>
                </a>

                <!-- Library Management -->
                <a href="{{ route('admin.books.index') }}" class="group bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-all duration-300">
                    <div class="p-6 text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-orange-100 group-hover:bg-orange-200 transition-colors">
                            <svg class="h-8 w-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Library</h3>
                        <p class="mt-2 text-sm text-gray-500">Manage books, issues, and library resources</p>
                        <div class="mt-4 text-orange-600 font-medium">View Details →</div>
                    </div>
                </a>

                <!-- Notices Management -->
                <a href="{{ route('admin.notices.index') }}" class="group bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-all duration-300">
                    <div class="p-6 text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-pink-100 group-hover:bg-pink-200 transition-colors">
                            <svg class="h-8 w-8 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.828 7l2.586 2.586a2 2 0 002.828 0L12 7l-1.758-1.758a2 2 0 00-2.828 0L4.828 7z"></path>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Notices</h3>
                        <p class="mt-2 text-sm text-gray-500">Create and manage university notices and announcements</p>
                        <div class="mt-4 text-pink-600 font-medium">View Details →</div>
                    </div>
                </a>

                <!-- Halls Management -->
                <a href="{{ route('admin.halls.index') }}" class="group bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-all duration-300">
                    <div class="p-6 text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-cyan-100 group-hover:bg-cyan-200 transition-colors">
                            <svg class="h-8 w-8 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Halls</h3>
                        <p class="mt-2 text-sm text-gray-500">Manage residential halls, facilities, and student assignments</p>
                        <div class="mt-4 text-cyan-600 font-medium">View Details →</div>
                    </div>
                </a>
            </div>

            <!-- Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Upcoming Exams -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Upcoming Exams</h3>
                        <div class="space-y-3">
                            @forelse($upcomingExams as $exam)
                                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $exam->title }}</p>
                                        <p class="text-sm text-gray-500">{{ $exam->course->name ?? 'N/A' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-600">{{ $exam->exam_date->format('M d, Y') }}</p>
                                        <p class="text-xs text-gray-500">{{ $exam->type }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">No upcoming exams scheduled.</p>
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
                                <div class="p-3 bg-blue-50 rounded-lg">
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
</x-app-layout>