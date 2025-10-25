<x-teacher-layout>
    <x-slot name="header">
        My Profile
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto">
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Profile Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-6">
                            <!-- Profile Picture -->
                            <div class="flex-shrink-0">
                                @if($teacher->user->profile_image)
                                    <img class="h-32 w-32 rounded-full object-cover border-4 border-blue-200" 
                                         src="{{ asset('storage/' . $teacher->user->profile_image) }}" 
                                         alt="{{ $teacher->user->name }}">
                                @else
                                    <div class="h-32 w-32 rounded-full bg-blue-500 flex items-center justify-center text-white text-4xl font-bold border-4 border-blue-200">
                                        {{ strtoupper(substr($teacher->user->name, 0, 2)) }}
                                    </div>
                                @endif
                            </div>

                            <!-- Basic Info -->
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900">{{ $teacher->user->name }}</h1>
                                <p class="text-lg text-gray-600 mt-1">{{ $teacher->department->name }}</p>
                                <p class="text-sm text-gray-500 mt-1">Employee ID: {{ $teacher->employee_id }}</p>
                                <div class="mt-3 flex space-x-2">
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ ucfirst($teacher->employment_type) }}
                                    </span>
                                    @if($teacher->is_active)
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex space-x-2">
                            <a href="{{ route('teacher.profile.edit') }}" 
                               class="bg-blue-600 hover:bg-blue-700 text-white text-sm py-1 px-3 rounded inline-flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit Profile
                            </a>
                            <a href="{{ route('teacher.profile.change-password') }}" 
                               class="bg-gray-600 hover:bg-gray-700 text-white text-sm py-1 px-3 rounded inline-flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                </svg>
                                Change Password
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Personal Information -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Contact Information -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-semibold text-gray-800">Contact Information</h2>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Email</label>
                                    <p class="mt-1 text-lg text-gray-900">{{ $teacher->user->email }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Phone</label>
                                    <p class="mt-1 text-lg text-gray-900">{{ $teacher->user->phone ?? 'Not provided' }}</p>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-500">Address</label>
                                    <p class="mt-1 text-lg text-gray-900">{{ $teacher->user->address ?? 'Not provided' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Professional Information -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-semibold text-gray-800">Professional Information</h2>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Department</label>
                                    <p class="mt-1 text-lg text-gray-900">{{ $teacher->department->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Employee ID</label>
                                    <p class="mt-1 text-lg text-gray-900">{{ $teacher->employee_id }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Qualification</label>
                                    <p class="mt-1 text-lg text-gray-900">{{ $teacher->qualification ?? 'Not provided' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Specialization</label>
                                    <p class="mt-1 text-lg text-gray-900">{{ $teacher->specialization ?? 'Not provided' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Joining Date</label>
                                    <p class="mt-1 text-lg text-gray-900">{{ $teacher->joining_date->format('F d, Y') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Employment Type</label>
                                    <p class="mt-1 text-lg text-gray-900">{{ ucfirst(str_replace('_', ' ', $teacher->employment_type)) }}</p>
                                </div>
                            </div>
                            @if($teacher->bio)
                                <div class="mt-6">
                                    <label class="block text-sm font-medium text-gray-500">Bio</label>
                                    <p class="mt-1 text-gray-900">{{ $teacher->bio }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Teaching Information -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-semibold text-gray-800">Teaching Assignments</h2>
                        </div>
                        <div class="p-6">
                            @if($teacher->courses->count() > 0)
                                <div class="space-y-3">
                                    @foreach($teacher->courses as $course)
                                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <h3 class="font-semibold text-gray-800">{{ $course->title }}</h3>
                                                    <p class="text-sm text-gray-600 mt-1">{{ $course->course_code }} | {{ $course->credits }} Credits</p>
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        Year {{ $course->academic_year }}, Semester {{ $course->semester }}
                                                    </p>
                                                </div>
                                                <a href="{{ route('teacher.courses.show', $course->id) }}" 
                                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                    View →
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 text-center py-8">No courses assigned yet.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar -->
                <div class="space-y-6">
                    <!-- Quick Stats -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-semibold text-gray-800">Quick Stats</h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Total Courses</label>
                                <p class="mt-1 text-3xl font-bold text-blue-600">{{ $teacher->courses->count() }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Total Credits</label>
                                <p class="mt-1 text-3xl font-bold text-green-600">{{ $teacher->courses->sum('credits') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Years of Service</label>
                                <p class="mt-1 text-3xl font-bold text-purple-600">
                                    {{ now()->diffInYears($teacher->joining_date) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-semibold text-gray-800">Quick Actions</h2>
                        </div>
                        <div class="p-6 space-y-2">
                            <a href="{{ route('teacher.courses.index') }}" 
                               class="block w-full text-center bg-blue-50 hover:bg-blue-100 text-blue-700 font-medium py-3 px-4 rounded">
                                View My Courses
                            </a>
                            <a href="{{ route('teacher.exams.create') }}" 
                               class="block w-full text-center bg-purple-50 hover:bg-purple-100 text-purple-700 font-medium py-3 px-4 rounded">
                                Create Assessment
                            </a>
                            <a href="{{ route('teacher.exams.index') }}" 
                               class="block w-full text-center bg-purple-50 hover:bg-purple-100 text-purple-700 font-medium py-3 px-4 rounded">
                                Manage Exams
                            </a>
                            <a href="{{ route('teacher.academic') }}" 
                               class="block w-full text-center bg-gray-50 hover:bg-gray-100 text-gray-700 font-medium py-3 px-4 rounded">
                                University Info
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-teacher-layout>

