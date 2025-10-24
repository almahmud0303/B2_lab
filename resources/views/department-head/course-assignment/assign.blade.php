<x-teacher-layout>
    <x-slot name="header">
        Assign Teacher - {{ $course->title }}
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto">
            <!-- Course Info -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-xl font-bold mb-4">Course Information</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div><strong>Title:</strong> {{ $course->title }}</div>
                    <div><strong>Code:</strong> {{ $course->course_code }}</div>
                    <div><strong>Credits:</strong> {{ $course->credits }}</div>
                    <div><strong>Year/Semester:</strong> {{ $course->academic_year }}/{{ $course->semester }}</div>
                    <div><strong>Max Students:</strong> {{ $course->max_students }}</div>
                    <div><strong>Current Teacher:</strong> {{ $course->teacher->user->name ?? 'None' }}</div>
                </div>
            </div>

            <!-- Teacher Selection -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold mb-4">Select Teacher</h2>
                <form method="POST" action="{{ route('department-head.course-assignment.update-assignment', $course->id) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-4">
                        @foreach($teachers as $teacher)
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-blue-50 {{ $course->teacher_id == $teacher->id ? 'border-blue-500 bg-blue-50' : '' }}">
                                <input type="radio" 
                                       name="teacher_id" 
                                       value="{{ $teacher->id }}" 
                                       class="mr-4"
                                       {{ $course->teacher_id == $teacher->id ? 'checked' : '' }}
                                       required>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900">{{ $teacher->user->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $teacher->employee_id }} | {{ $teacher->specialization ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Current Load: {{ $teacher->current_courses_count }} courses, {{ $teacher->total_students }} students
                                    </p>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <a href="{{ route('department-head.course-assignment.index') }}" 
                           class="bg-gray-200 px-6 py-2 rounded">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="bg-blue-600 text-white px-6 py-2 rounded">
                            Assign Teacher
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-teacher-layout>

