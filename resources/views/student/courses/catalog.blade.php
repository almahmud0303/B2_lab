<x-student-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Optional Courses Catalog') }}
            </h2>
            <a href="{{ route('student.courses.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to My Courses
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Info Banner -->
            <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">About Optional Courses</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>Optional courses (electives) allow you to specialize in areas of interest. You can enroll in any optional course for your current year and semester, subject to availability and prerequisites.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Available Optional Courses -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Available Optional Courses for {{ $student->academic_year }} Year, {{ $student->semester }} Semester
                    </h3>

                    @if($availableCourses->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($availableCourses as $course)
                                <div class="border border-gray-200 rounded-lg p-5 hover:shadow-lg transition-shadow">
                                    <!-- Course Header -->
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex-1">
                                            <h4 class="text-lg font-semibold text-gray-900">{{ $course->title }}</h4>
                                            <p class="text-sm text-gray-600 mt-1">{{ $course->course_code }}</p>
                                        </div>
                                        <span class="ml-2 px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            Optional
                                        </span>
                                    </div>

                                    <!-- Course Details -->
                                    <div class="space-y-2 mb-4">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Teacher:</span>
                                            <span class="font-medium text-gray-900">{{ $course->teacher->user->name }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Credits:</span>
                                            <span class="font-medium text-gray-900">{{ $course->credits }} Credit Hours</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Seats Available:</span>
                                            <span class="font-medium {{ $course->slots_remaining > 5 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $course->slots_remaining }} / {{ $course->max_enrollments ?? $course->max_students }}
                                            </span>
                                        </div>
                                        @if($course->prerequisites)
                                            <div class="text-sm">
                                                <span class="text-gray-600">Prerequisites:</span>
                                                <span class="font-medium text-orange-600">{{ $course->prerequisites }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Course Description -->
                                    @if($course->description)
                                        <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $course->description }}</p>
                                    @endif

                                    <!-- Enrollment Status -->
                                    @if($course->slots_remaining > 0)
                                        <form method="POST" action="{{ route('student.courses.enroll', $course) }}">
                                            @csrf
                                            <button type="submit" 
                                                    class="w-full bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded transition-colors">
                                                Enroll in This Course
                                            </button>
                                        </form>
                                    @else
                                        <button disabled 
                                                class="w-full bg-gray-300 text-gray-500 font-medium py-2 px-4 rounded cursor-not-allowed">
                                            Course Full - No Seats Available
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">No Optional Courses Available</h3>
                            <p class="mt-2 text-sm text-gray-500">
                                There are no optional courses available for your current academic year ({{ $student->academic_year }}) and semester ({{ $student->semester }}).
                            </p>
                            <p class="mt-1 text-sm text-gray-500">
                                Optional courses will be available in later semesters, especially in 4th year.
                            </p>
                            <div class="mt-6">
                                <a href="{{ route('student.courses.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-700 text-white font-bold rounded">
                                    Back to My Courses
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-student-layout>

