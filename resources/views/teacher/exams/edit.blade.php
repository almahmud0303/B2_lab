<x-teacher-layout>
    <x-slot name="header">Edit {{ ucfirst($exam->type) }}</x-slot>
    <div class="py-6">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <form method="POST" action="{{ route('teacher.exams.update', $exam->id) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
                            <select name="type" class="w-full border rounded px-4 py-2" required {{ $exam->type === 'final' ? 'disabled' : '' }}>
                                <option value="quiz" {{ $exam->type === 'quiz' ? 'selected' : '' }}>Quiz</option>
                                <option value="assignment" {{ $exam->type === 'assignment' ? 'selected' : '' }}>Assignment</option>
                                <option value="midterm" {{ $exam->type === 'midterm' ? 'selected' : '' }}>Midterm Exam</option>
                                @if($exam->type === 'final')
                                    <option value="final" selected>Final Exam (Admin Only)</option>
                                @endif
                            </select>
                            @if($exam->type === 'final')
                                <input type="hidden" name="type" value="final">
                                <p class="text-xs text-red-500 mt-1">Note: Final exams can only be modified by admin</p>
                            @else
                                <p class="text-xs text-gray-500 mt-1">Note: Final exams are set by admin only</p>
                            @endif
                            @error('type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                            <input type="text" name="title" value="{{ old('title', $exam->title) }}" class="w-full border rounded px-4 py-2" required>
                            @error('title')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date *</label>
                            <input type="date" name="exam_date" value="{{ old('exam_date', $exam->exam_date->format('Y-m-d')) }}" class="w-full border rounded px-4 py-2" required>
                            @error('exam_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Start Time *</label>
                            <input type="time" name="start_time" value="{{ old('start_time', \Carbon\Carbon::parse($exam->start_time)->format('H:i')) }}" class="w-full border rounded px-4 py-2" required>
                            @error('start_time')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">End Time *</label>
                            <input type="time" name="end_time" value="{{ old('end_time', \Carbon\Carbon::parse($exam->end_time)->format('H:i')) }}" class="w-full border rounded px-4 py-2" required>
                            @error('end_time')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Total Marks *</label>
                            <input type="number" name="total_marks" value="{{ old('total_marks', $exam->total_marks) }}" class="w-full border rounded px-4 py-2" min="1" required>
                            @error('total_marks')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Venue/Room</label>
                            <input type="text" name="venue" value="{{ old('venue', $exam->venue) }}" class="w-full border rounded px-4 py-2">
                            @error('venue')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                        <select name="status" class="w-full border rounded px-4 py-2" required>
                            <option value="scheduled" {{ $exam->status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="ongoing" {{ $exam->status === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                            <option value="completed" {{ $exam->status === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $exam->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description/Instructions</label>
                        <textarea name="description" rows="4" class="w-full border rounded px-4 py-2">{{ old('description', $exam->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('teacher.exams.show', $exam->id) }}" class="bg-gray-200 px-6 py-2 rounded hover:bg-gray-300">Cancel</a>
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-teacher-layout>
