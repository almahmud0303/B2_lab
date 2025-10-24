<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Course Organization') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.courses.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Add New Course
                </a>
                <a href="{{ route('admin.courses.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    View All Courses
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Department Selector -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.courses.organize') }}" class="flex space-x-4">
                        <div class="flex-1">
                            <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">Select Department</label>
                            <select id="department_id" name="department_id" onchange="this.form.submit()"
                                    class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Departments</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }} ({{ $dept->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Courses by Year and Semester -->
            @foreach($years as $year)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">
                            {{ $year }} Year
                        </h3>

                        <!-- 2 Semesters per Year -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @php
                                $yearNum = (int)str_replace(['st', 'nd', 'rd', 'th'], '', $year);
                                $sem1 = (($yearNum - 1) * 2) + 1;
                                $sem2 = (($yearNum - 1) * 2) + 2;
                                $semesters = [
                                    ['1st', '2nd', '3rd', '4th', '5th', '6th', '7th', '8th'][$sem1 - 1],
                                    ['1st', '2nd', '3rd', '4th', '5th', '6th', '7th', '8th'][$sem2 - 1],
                                ];
                            @endphp

                            @foreach($semesters as $semester)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <h4 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">
                                        {{ $semester}} Semester
                                    </h4>

                                    @php
                                        $semesterCourses = $coursesByYearSemester[$year][$semester] ?? collect();
                                    @endphp

                                    @if($semesterCourses->count() > 0)
                                        <div class="space-y-3">
                                            <!-- Compulsory Courses -->
                                            @php
                                                $compulsory = $semesterCourses->where('course_type', 'compulsory');
                                                $optional = $semesterCourses->where('course_type', 'optional');
                                            @endphp

                                            @if($compulsory->count() > 0)
                                                <div>
                                                    <p class="text-xs font-semibold text-red-600 uppercase mb-2">Compulsory ({{ $compulsory->count() }})</p>
                                                    <div class="space-y-2">
                                                        @foreach($compulsory as $course)
                                                            <div class="bg-red-50 p-3 rounded border-l-4 border-red-500">
                                                                <div class="flex justify-between items-start">
                                                                    <div class="flex-1">
                                                                        <p class="font-medium text-gray-900 text-sm">{{ $course->course_code }}</p>
                                                                        <p class="text-xs text-gray-600">{{ Str::limit($course->title, 40) }}</p>
                                                                        <p class="text-xs text-gray-500 mt-1">{{ $course->teacher->user->name }}</p>
                                                                    </div>
                                                                    <div class="text-right ml-2">
                                                                        <span class="text-xs font-semibold text-red-700">{{ $course->credits }} cr</span>
                                                                        <div class="mt-1">
                                                                            <a href="{{ route('admin.courses.edit', $course) }}" class="text-xs text-blue-600 hover:text-blue-900">Edit</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            @if($optional->count() > 0)
                                                <div>
                                                    <p class="text-xs font-semibold text-green-600 uppercase mb-2">Optional ({{ $optional->count() }})</p>
                                                    <div class="space-y-2">
                                                        @foreach($optional as $course)
                                                            <div class="bg-green-50 p-3 rounded border-l-4 border-green-500">
                                                                <div class="flex justify-between items-start">
                                                                    <div class="flex-1">
                                                                        <p class="font-medium text-gray-900 text-sm">{{ $course->course_code }}</p>
                                                                        <p class="text-xs text-gray-600">{{ Str::limit($course->title, 40) }}</p>
                                                                        <p class="text-xs text-gray-500 mt-1">{{ $course->teacher->user->name }}</p>
                                                                        @if($course->max_enrollments)
                                                                            <p class="text-xs text-orange-600 mt-1">Max: {{ $course->max_enrollments }} students</p>
                                                                        @endif
                                                                    </div>
                                                                    <div class="text-right ml-2">
                                                                        <span class="text-xs font-semibold text-green-700">{{ $course->credits }} cr</span>
                                                                        <div class="mt-1">
                                                                            <a href="{{ route('admin.courses.edit', $course) }}" class="text-xs text-blue-600 hover:text-blue-900">Edit</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-center py-8">
                                            <p class="text-sm text-gray-400">No courses for this semester</p>
                                            <a href="{{ route('admin.courses.create') }}" class="text-sm text-blue-600 hover:text-blue-900 mt-2 inline-block">
                                                + Add Course
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>

