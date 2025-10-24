<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Course Details') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.courses.edit', $course) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Edit Course
                </a>
                <a href="{{ route('admin.courses.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Course Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Course Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Course Code</label>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $course->course_code }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Course Title</label>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $course->title }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Department</label>
                            <p class="mt-1 text-base text-gray-900">{{ $course->department->name }} ({{ $course->department->code }})</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Course Teacher</label>
                            <p class="mt-1 text-base text-gray-900">{{ $course->teacher->user->name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Academic Year / Semester</label>
                            <p class="mt-1 text-base text-gray-900">{{ $course->academic_year }} Year / {{ $course->semester }} Semester</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Credits</label>
                            <p class="mt-1 text-base text-gray-900">{{ $course->credits }} Credit Hours</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Course Type</label>
                            <p class="mt-1">
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                                    {{ $course->course_type === 'compulsory' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                    {{ ucfirst($course->course_type) }}
                                </span>
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status</label>
                            <p class="mt-1">
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                                    {{ $course->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $course->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Max Students</label>
                            <p class="mt-1 text-base text-gray-900">{{ $course->max_students }} students</p>
                        </div>

                        @if($course->max_enrollments)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Max Enrollments (Optional)</label>
                            <p class="mt-1 text-base text-gray-900">{{ $course->max_enrollments }} students</p>
                        </div>
                        @endif
                    </div>

                    @if($course->description)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-500">Description</label>
                        <p class="mt-1 text-base text-gray-700">{{ $course->description }}</p>
                    </div>
                    @endif

                    @if($course->prerequisites)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-500">Prerequisites</label>
                        <p class="mt-1 text-base text-gray-700">{{ $course->prerequisites }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Enrollment Statistics -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Enrollment Statistics</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">Total Enrollments</p>
                            <p class="text-2xl font-bold text-blue-600">{{ $enrollmentStats['total'] }}</p>
                        </div>

                        <div class="bg-green-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">Currently Enrolled</p>
                            <p class="text-2xl font-bold text-green-600">{{ $enrollmentStats['enrolled'] }}</p>
                        </div>

                        <div class="bg-purple-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">Completed</p>
                            <p class="text-2xl font-bold text-purple-600">{{ $enrollmentStats['completed'] }}</p>
                        </div>

                        <div class="bg-red-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">Dropped</p>
                            <p class="text-2xl font-bold text-red-600">{{ $enrollmentStats['dropped'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enrolled Students -->
            @if($course->enrollments->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Enrolled Students</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Enrollment Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grade</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($course->enrollments->take(20) as $enrollment)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $enrollment->student->student_id }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                        {{ $enrollment->student->user->name }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                        {{ $enrollment->enrollment_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $enrollment->status === 'enrolled' ? 'bg-green-100 text-green-800' : 
                                               ($enrollment->status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                                            {{ ucfirst($enrollment->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                        {{ $enrollment->grade ?? '-' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($course->enrollments->count() > 20)
                    <p class="mt-4 text-sm text-gray-600">Showing first 20 of {{ $course->enrollments->count() }} enrollments</p>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>

