<x-student-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Academic Information') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Student Academic Profile -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">My Academic Profile</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="border-l-4 border-blue-500 pl-4">
                            <p class="text-sm text-gray-600">Student ID</p>
                            <p class="text-lg font-semibold text-gray-900">{{ Auth::user()->student->student_id }}</p>
                        </div>
                        <div class="border-l-4 border-green-500 pl-4">
                            <p class="text-sm text-gray-600">Department</p>
                            <p class="text-lg font-semibold text-gray-900">{{ Auth::user()->student->department->name }}</p>
                            <p class="text-xs text-gray-500">{{ Auth::user()->student->department->code }}</p>
                        </div>
                        <div class="border-l-4 border-purple-500 pl-4">
                            <p class="text-sm text-gray-600">Current Year</p>
                            <p class="text-lg font-semibold text-gray-900">{{ Auth::user()->student->academic_year }} Year</p>
                        </div>
                        <div class="border-l-4 border-orange-500 pl-4">
                            <p class="text-sm text-gray-600">Current Semester</p>
                            <p class="text-lg font-semibold text-gray-900">{{ Auth::user()->student->semester }} Semester</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Courses & Teachers -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Current Courses & Teachers</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course Code</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course Title</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teacher</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Credits</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php
                                    $currentEnrollments = Auth::user()->student->enrollments()
                                        ->with(['course.teacher.user', 'course.department'])
                                        ->where('status', 'enrolled')
                                        ->get();
                                @endphp
                                @forelse($currentEnrollments as $enrollment)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $enrollment->course->course_code }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $enrollment->course->title }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            {{ $enrollment->course->teacher->user->name }}
                                            <div class="text-xs text-gray-500">{{ $enrollment->course->teacher->designation ?? 'Lecturer' }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $enrollment->course->credits }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs rounded-full {{ $enrollment->course->course_type === 'compulsory' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                                {{ ucfirst($enrollment->course->course_type) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                            No enrolled courses found. Please contact your department.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Department Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Department Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">{{ Auth::user()->student->department->name }}</h4>
                            <div class="space-y-2 text-sm text-gray-600">
                                <p><strong>Code:</strong> {{ Auth::user()->student->department->code }}</p>
                                <p><strong>Building:</strong> {{ Auth::user()->student->department->building ?? 'Academic Building' }}</p>
                                @if(Auth::user()->student->department->head)
                                    <p><strong>Head of Department:</strong> {{ Auth::user()->student->department->head }}</p>
                                @endif
                            </div>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">Department Teachers</h4>
                            <div class="space-y-2">
                                @php
                                    $teachers = \App\Models\Teacher::where('department_id', Auth::user()->student->department_id)
                                        ->with('user')
                                        ->take(5)
                                        ->get();
                                @endphp
                                @foreach($teachers as $teacher)
                                    <div class="flex items-center space-x-2 text-sm">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                            <span class="text-xs font-medium text-blue-600">{{ strtoupper(substr($teacher->user->name, 0, 2)) }}</span>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $teacher->user->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $teacher->designation ?? 'Lecturer' }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- University Administration -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">University Administration</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="border rounded-lg p-4 text-center">
                            <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-blue-100 flex items-center justify-center">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <h4 class="font-semibold text-gray-900">Vice-Chancellor</h4>
                            <p class="text-sm text-gray-600 mt-1">Prof. Dr. Mihir Ranjan Halder</p>
                            <p class="text-xs text-gray-500 mt-1">Khulna University of Engineering & Technology</p>
                        </div>
                        <div class="border rounded-lg p-4 text-center">
                            <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-green-100 flex items-center justify-center">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h4 class="font-semibold text-gray-900">Pro Vice-Chancellor</h4>
                            <p class="text-sm text-gray-600 mt-1">Prof. Dr. Kazi Md. Shorowardy</p>
                            <p class="text-xs text-gray-500 mt-1">Academic Affairs</p>
                        </div>
                        <div class="border rounded-lg p-4 text-center">
                            <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-purple-100 flex items-center justify-center">
                                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                                </svg>
                            </div>
                            <h4 class="font-semibold text-gray-900">Registrar</h4>
                            <p class="text-sm text-gray-600 mt-1">Md. Shahjahan Alam</p>
                            <p class="text-xs text-gray-500 mt-1">Administration</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- University Contact & Resources -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Contact Information -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <div>
                                    <p class="font-medium text-gray-900">Address</p>
                                    <p class="text-gray-600">Khulna University of Engineering & Technology</p>
                                    <p class="text-gray-600">Fulbarigate, Khulna-9203, Bangladesh</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <div>
                                    <p class="font-medium text-gray-900">Phone</p>
                                    <p class="text-gray-600">+880-41-769471-5</p>
                                    <p class="text-gray-600">PABX: +880-41-769468</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <div>
                                    <p class="font-medium text-gray-900">Email</p>
                                    <p class="text-gray-600">registrar@kuet.ac.bd</p>
                                    <p class="text-gray-600">info@kuet.ac.bd</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                                </svg>
                                <div>
                                    <p class="font-medium text-gray-900">Website</p>
                                    <a href="https://www.kuet.ac.bd" target="_blank" class="text-blue-600 hover:text-blue-800">www.kuet.ac.bd</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Links & Resources</h3>
                        <div class="space-y-2">
                            <a href="#" class="flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition">
                                <span class="text-sm font-medium text-gray-900">Academic Calendar</span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                            <a href="#" class="flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition">
                                <span class="text-sm font-medium text-gray-900">Library Resources</span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                            <a href="{{ route('student.courses.index') }}" class="flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition">
                                <span class="text-sm font-medium text-gray-900">My Courses</span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                            <a href="{{ route('student.results.index') }}" class="flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition">
                                <span class="text-sm font-medium text-gray-900">Exam Results</span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                            <a href="{{ route('student.notices.index') }}" class="flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition">
                                <span class="text-sm font-medium text-gray-900">Notice Board</span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                            <a href="{{ route('student.fees.index') }}" class="flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition">
                                <span class="text-sm font-medium text-gray-900">Fee Payment</span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-student-layout>
