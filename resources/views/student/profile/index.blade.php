<x-student-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Profile') }}
            </h2>
            <div class="flex space-x-3 ml-auto">
                <a href="{{ route('student.profile.edit') }}" 
                   class="bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium py-1.5 px-3 rounded-md inline-flex items-center transition-colors duration-150">
                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Profile
                </a>
                <a href="{{ route('student.profile.change-password') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white text-xs font-medium py-1.5 px-3 rounded-md inline-flex items-center transition-colors duration-150">
                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                    Change Password
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Profile Overview -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center space-x-6">
                        <!-- Profile Picture -->
                        <div class="flex-shrink-0">
                            @if($student->user->profile_image)
                                <img class="h-32 w-32 rounded-full object-cover" 
                                     src="{{ asset('storage/' . $student->user->profile_image) }}" 
                                     alt="{{ $student->user->name }}">
                            @else
                                <div class="h-32 w-32 rounded-full bg-blue-500 flex items-center justify-center text-white text-4xl font-bold">
                                    {{ strtoupper(substr($student->user->name, 0, 2)) }}
                                </div>
                            @endif
                        </div>

                        <!-- Basic Info -->
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold text-gray-900">{{ $student->user->name }}</h3>
                            <p class="text-gray-600">{{ $student->user->email }}</p>
                            <p class="text-gray-600">Student ID: {{ $student->student_id }}</p>
                            <div class="mt-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    {{ $student->status === 'active' ? 'Active' : ucfirst($student->status) }}
                                </span>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="hidden lg:grid grid-cols-3 gap-4 text-center">
                            <div class="border-l-2 border-blue-500 pl-4">
                                <div class="text-2xl font-bold text-gray-900">{{ $stats['total_enrollments'] ?? 0 }}</div>
                                <div class="text-xs text-gray-600">Total Courses</div>
                            </div>
                            <div class="border-l-2 border-green-500 pl-4">
                                <div class="text-2xl font-bold text-gray-900">{{ $stats['completed_courses'] ?? 0 }}</div>
                                <div class="text-xs text-gray-600">Completed</div>
                            </div>
                            <div class="border-l-2 border-purple-500 pl-4">
                                <div class="text-2xl font-bold text-gray-900">{{ $stats['total_credits'] ?? 0 }}</div>
                                <div class="text-xs text-gray-600">Credits</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Academic Details</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Department</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $student->department->name ?? 'Not Assigned' }}</dd>
                                <dd class="text-xs text-gray-500">{{ $student->department->code ?? '' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Student ID</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $student->student_id }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Admission Number</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $student->admission_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Academic Year</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $student->academic_year }} Year</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Current Semester</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $student->semester }} Semester</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Admission Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $student->admission_date ? $student->admission_date->format('F d, Y') : 'N/A' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Phone Number</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $student->user->phone ?? 'Not Provided' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Date of Birth</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $student->user->date_of_birth ? $student->user->date_of_birth->format('F d, Y') : 'Not Provided' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Gender</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $student->user->gender ? ucfirst($student->user->gender) : 'Not Specified' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Address</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $student->user->address ?? 'Not Provided' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Guardian Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Guardian Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Guardian Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $student->guardian_name ?? 'Not Provided' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Guardian Phone</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $student->guardian_phone ?? 'Not Provided' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Guardian Address</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $student->guardian_address ?? 'Not Provided' }}</dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hall Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Hall Assignment</h3>
                    @if($student->hall)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Assigned Hall</dt>
                                <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $student->hall->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Hall Location</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $student->hall->location ?? 'Not specified' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Hall Capacity</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $student->hall->capacity }} students</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Hall Status</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if(!$student->hall->is_active) bg-gray-100 text-gray-800
                                        @elseif($student->hall->is_available) bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ $student->hall->status }}
                                    </span>
                                </dd>
                            </div>
                        </div>
                        @if($student->hall->description)
                            <div class="mt-4">
                                <dt class="text-sm font-medium text-gray-500">Hall Description</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $student->hall->description }}</dd>
                            </div>
                        @endif
                        @php
                            $hallFacilities = is_array($student->hall->facilities) ? $student->hall->facilities : json_decode($student->hall->facilities, true);
                            $hallFacilities = $hallFacilities ?: [];
                        @endphp
                        @if(count($hallFacilities) > 0)
                            <div class="mt-4">
                                <dt class="text-sm font-medium text-gray-500">Available Facilities</dt>
                                <dd class="mt-1">
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($hallFacilities as $facility)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $facility }}
                                            </span>
                                        @endforeach
                                    </div>
                                </dd>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No Hall Assigned</h3>
                            <p class="mt-1 text-sm text-gray-500">You have not been assigned to any hall yet.</p>
                            <div class="mt-6">
                                <a href="{{ route('student.halls.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    View Available Halls
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <a href="{{ route('student.profile.academic') }}" class="group flex flex-col items-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition-all duration-200 transform hover:scale-[1.02]">
                            <div class="p-2 bg-blue-100 rounded-lg group-hover:bg-blue-200 transition-colors duration-200">
                                <svg class="w-6 h-6 text-blue-600 group-hover:text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <span class="mt-2 text-sm font-medium text-gray-900 group-hover:text-gray-800">Academic Info</span>
                        </a>
                        <a href="{{ route('student.courses.index') }}" class="group flex flex-col items-center p-4 bg-green-50 hover:bg-green-100 rounded-lg transition-all duration-200 transform hover:scale-[1.02]">
                            <div class="p-2 bg-green-100 rounded-lg group-hover:bg-green-200 transition-colors duration-200">
                                <svg class="w-6 h-6 text-green-600 group-hover:text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <span class="mt-2 text-sm font-medium text-gray-900 group-hover:text-gray-800">My Courses</span>
                        </a>
                        <a href="{{ route('student.results.index') }}" class="group flex flex-col items-center p-4 bg-purple-50 hover:bg-purple-100 rounded-lg transition-all duration-200 transform hover:scale-[1.02]">
                            <div class="p-2 bg-purple-100 rounded-lg group-hover:bg-purple-200 transition-colors duration-200">
                                <svg class="w-6 h-6 text-purple-600 group-hover:text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <span class="mt-2 text-sm font-medium text-gray-900 group-hover:text-gray-800">Results</span>
                        </a>
                        <a href="{{ route('student.fees.index') }}" class="group flex flex-col items-center p-4 bg-yellow-50 hover:bg-yellow-100 rounded-lg transition-all duration-200 transform hover:scale-[1.02]">
                            <div class="p-2 bg-yellow-100 rounded-lg group-hover:bg-yellow-200 transition-colors duration-200">
                                <svg class="w-6 h-6 text-yellow-600 group-hover:text-yellow-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <span class="mt-2 text-sm font-medium text-gray-900 group-hover:text-gray-800">Fees</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-student-layout>
