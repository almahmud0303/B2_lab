<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create New Course') }}
            </h2>
            <a href="{{ route('admin.courses.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Courses
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.courses.store') }}" class="space-y-6">
                        @csrf

                        <!-- Course Code and Title -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="course_code" class="block text-sm font-medium text-gray-700">Course Code *</label>
                                <input type="text" id="course_code" name="course_code" value="{{ old('course_code') }}" required
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('course_code') border-red-500 @enderror">
                                @error('course_code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700">Course Title *</label>
                                <input type="text" id="title" name="title" value="{{ old('title') }}" required
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-500 @enderror">
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Department and Teacher -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="department_id" class="block text-sm font-medium text-gray-700">Department *</label>
                                <select id="department_id" name="department_id" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('department_id') border-red-500 @enderror">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }} ({{ $department->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="teacher_id" class="block text-sm font-medium text-gray-700">Course Teacher *</label>
                                <select id="teacher_id" name="teacher_id" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('teacher_id') border-red-500 @enderror">
                                    <option value="">Select Teacher</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                            {{ $teacher->user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('teacher_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Academic Year and Semester -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="academic_year" class="block text-sm font-medium text-gray-700">Academic Year *</label>
                                <select id="academic_year" name="academic_year" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('academic_year') border-red-500 @enderror">
                                    <option value="">Select Year</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year }}" {{ old('academic_year') === $year ? 'selected' : '' }}>
                                            {{ $year }} Year
                                        </option>
                                    @endforeach
                                </select>
                                @error('academic_year')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="semester" class="block text-sm font-medium text-gray-700">Semester *</label>
                                <select id="semester" name="semester" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('semester') border-red-500 @enderror">
                                    <option value="">Select Semester</option>
                                    @foreach($semesters as $sem)
                                        <option value="{{ $sem }}" {{ old('semester') === $sem ? 'selected' : '' }}>
                                            {{ $sem }} Semester
                                        </option>
                                    @endforeach
                                </select>
                                @error('semester')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="credits" class="block text-sm font-medium text-gray-700">Credits *</label>
                                <input type="number" id="credits" name="credits" value="{{ old('credits') }}" min="1" max="6" required
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('credits') border-red-500 @enderror">
                                @error('credits')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Course Type and Enrollment Limits -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="course_type" class="block text-sm font-medium text-gray-700">Course Type *</label>
                                <select id="course_type" name="course_type" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('course_type') border-red-500 @enderror">
                                    <option value="compulsory" {{ old('course_type') === 'compulsory' ? 'selected' : '' }}>Compulsory</option>
                                    <option value="optional" {{ old('course_type') === 'optional' ? 'selected' : '' }}>Optional</option>
                                </select>
                                @error('course_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Compulsory courses are auto-enrolled for all students</p>
                            </div>

                            <div>
                                <label for="max_students" class="block text-sm font-medium text-gray-700">Max Students *</label>
                                <input type="number" id="max_students" name="max_students" value="{{ old('max_students', 50) }}" min="1" required
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('max_students') border-red-500 @enderror">
                                @error('max_students')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="max_enrollments" class="block text-sm font-medium text-gray-700">Max Enrollments (Optional)</label>
                                <input type="number" id="max_enrollments" name="max_enrollments" value="{{ old('max_enrollments') }}" min="1"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('max_enrollments') border-red-500 @enderror">
                                @error('max_enrollments')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">For optional courses only</p>
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Course Description</label>
                            <textarea id="description" name="description" rows="4"
                                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Prerequisites -->
                        <div>
                            <label for="prerequisites" class="block text-sm font-medium text-gray-700">Prerequisites</label>
                            <textarea id="prerequisites" name="prerequisites" rows="2"
                                      placeholder="e.g., CSE1101, CSE1201 (comma separated course codes)"
                                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('prerequisites') border-red-500 @enderror">{{ old('prerequisites') }}</textarea>
                            @error('prerequisites')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Enter course codes that must be completed before enrolling in this course</p>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('admin.courses.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded">
                                Cancel
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                                Create Course
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
