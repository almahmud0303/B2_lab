<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Edit Assessment') }} - {{ $exam->title }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $department->name ?? 'No Department Assigned' }}
                </p>
            </div>
            <a href="{{ route('department-head.exams.show', $exam->id) }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                Back to Assessment
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-6">Edit Assessment</h3>
                    
                    <form action="{{ route('department-head.exams.update', $exam->id) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Assessment Type *</label>
                                <select name="type" id="type" class="w-full border rounded px-4 py-2 @error('type') border-red-500 @enderror" required>
                                    <option value="quiz" {{ $exam->type === 'quiz' ? 'selected' : '' }}>Quiz</option>
                                    <option value="assignment" {{ $exam->type === 'assignment' ? 'selected' : '' }}>Assignment</option>
                                    <option value="midterm" {{ $exam->type === 'midterm' ? 'selected' : '' }}>Midterm Exam</option>
                                    <option value="final" {{ $exam->type === 'final' ? 'selected' : '' }}>Final Exam</option>
                                </select>
                                @error('type')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Assessment Title *</label>
                                <input type="text" name="title" id="title" value="{{ old('title', $exam->title) }}" 
                                       class="w-full border rounded px-4 py-2 @error('title') border-red-500 @enderror" required>
                                @error('title')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="exam_date" class="block text-sm font-medium text-gray-700 mb-2">Date *</label>
                                <input type="date" name="exam_date" id="exam_date" value="{{ old('exam_date', $exam->exam_date->format('Y-m-d')) }}" 
                                       class="w-full border rounded px-4 py-2 @error('exam_date') border-red-500 @enderror" required>
                                @error('exam_date')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">Start Time *</label>
                                <input type="time" name="start_time" id="start_time" value="{{ old('start_time', \Carbon\Carbon::parse($exam->start_time)->format('H:i')) }}" 
                                       class="w-full border rounded px-4 py-2 @error('start_time') border-red-500 @enderror" required>
                                @error('start_time')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">End Time *</label>
                                <input type="time" name="end_time" id="end_time" value="{{ old('end_time', \Carbon\Carbon::parse($exam->end_time)->format('H:i')) }}" 
                                       class="w-full border rounded px-4 py-2 @error('end_time') border-red-500 @enderror" required>
                                @error('end_time')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="total_marks" class="block text-sm font-medium text-gray-700 mb-2">Total Marks *</label>
                                <input type="number" name="total_marks" id="total_marks" value="{{ old('total_marks', $exam->total_marks) }}" 
                                       class="w-full border rounded px-4 py-2 @error('total_marks') border-red-500 @enderror" 
                                       min="1" required>
                                @error('total_marks')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="venue" class="block text-sm font-medium text-gray-700 mb-2">Venue/Room</label>
                                <input type="text" name="venue" id="venue" value="{{ old('venue', $exam->venue) }}" 
                                       class="w-full border rounded px-4 py-2 @error('venue') border-red-500 @enderror">
                                @error('venue')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                            <select name="status" id="status" class="w-full border rounded px-4 py-2 @error('status') border-red-500 @enderror" required>
                                <option value="scheduled" {{ $exam->status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="ongoing" {{ $exam->status === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                <option value="completed" {{ $exam->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $exam->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description/Instructions</label>
                            <textarea name="description" id="description" rows="4" 
                                      class="w-full border rounded px-4 py-2 @error('description') border-red-500 @enderror">{{ old('description', $exam->description) }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end gap-4">
                            <a href="{{ route('department-head.exams.show', $exam->id) }}" 
                               class="bg-gray-200 px-6 py-2 rounded hover:bg-gray-300">Cancel</a>
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                                Update Assessment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
