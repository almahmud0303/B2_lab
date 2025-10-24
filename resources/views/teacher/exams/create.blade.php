<x-teacher-layout>
    <x-slot name="header">Create Quiz/Assignment/Midterm</x-slot>
    <div class="py-6">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <form method="POST" action="{{ route('teacher.exams.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Course</label>
                        <select name="course_id" class="w-full border rounded px-4 py-2" required>
                            <option value="">Select Course</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->title }} ({{ $course->course_code }})</option>
                            @endforeach
                        </select>
                        @error('course_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
                            <select name="type" class="w-full border rounded px-4 py-2" required>
                                <option value="">Select Type</option>
                                <option value="quiz">Quiz</option>
                                <option value="assignment">Assignment</option>
                                <option value="midterm">Midterm Exam</option>
                            </select>
                            @error('type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Note: Final exams are set by admin only</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                            <input type="text" name="title" value="{{ old('title') }}" class="w-full border rounded px-4 py-2" placeholder="e.g., Quiz 1, Midterm" required>
                            @error('title')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date *</label>
                            <input type="date" name="exam_date" value="{{ old('exam_date') }}" class="w-full border rounded px-4 py-2" required>
                            @error('exam_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Start Time *</label>
                            <input type="time" name="start_time" value="{{ old('start_time') }}" class="w-full border rounded px-4 py-2" required>
                            @error('start_time')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">End Time *</label>
                            <input type="time" name="end_time" value="{{ old('end_time') }}" class="w-full border rounded px-4 py-2" required>
                            @error('end_time')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Total Marks *</label>
                            <input type="number" name="total_marks" value="{{ old('total_marks') }}" class="w-full border rounded px-4 py-2" min="1" required>
                            @error('total_marks')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Venue/Room</label>
                            <input type="text" name="venue" value="{{ old('venue') }}" class="w-full border rounded px-4 py-2" placeholder="e.g., Room 301">
                            @error('venue')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description/Instructions</label>
                        <textarea name="description" rows="4" class="w-full border rounded px-4 py-2" placeholder="Enter instructions, topics covered, or submission guidelines...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('teacher.exams.index') }}" class="bg-gray-200 px-6 py-2 rounded hover:bg-gray-300">Cancel</a>
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-teacher-layout>