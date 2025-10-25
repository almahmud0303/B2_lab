<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Create Department Assessment') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $department->name ?? 'No Department Assigned' }}
                </p>
            </div>
            <a href="{{ route('department-head.exams.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                Back to Assessments
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Department Information -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h4 class="text-sm font-medium text-blue-800">Department Assessment</h4>
                        <p class="text-sm text-blue-600">Creating assessment for courses in: <strong>{{ $department->name ?? 'No Department Assigned' }}</strong></p>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-6">Create Department Assessment</h3>
                    
                    <form action="{{ route('department-head.exams.store') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">Course *</label>
                                <select name="course_id" id="course_id" class="w-full border rounded px-4 py-2 @error('course_id') border-red-500 @enderror" required>
                                    <option value="">Select Course</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                            {{ $course->title }} ({{ $course->course_code }})
                                            @if($course->teacher)
                                                - {{ $course->teacher->user->name }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('course_id')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Assessment Type *</label>
                                <select name="type" id="type" class="w-full border rounded px-4 py-2 @error('type') border-red-500 @enderror" required>
                                    <option value="">Select Type</option>
                                    <option value="quiz" {{ old('type') == 'quiz' ? 'selected' : '' }}>Quiz</option>
                                    <option value="assignment" {{ old('type') == 'assignment' ? 'selected' : '' }}>Assignment</option>
                                    <option value="midterm" {{ old('type') == 'midterm' ? 'selected' : '' }}>Midterm Exam</option>
                                    <option value="final" {{ old('type') == 'final' ? 'selected' : '' }}>Final Exam</option>
                                </select>
                                @error('type')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-gray-500 mt-1">Department heads can create all types of assessments</p>
                            </div>
                        </div>

                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Assessment Title *</label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" 
                                   class="w-full border rounded px-4 py-2 @error('title') border-red-500 @enderror" 
                                   placeholder="e.g., Quiz 1, Midterm Exam, Final Exam" required>
                            @error('title')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="exam_date" class="block text-sm font-medium text-gray-700 mb-2">Date *</label>
                                <input type="date" name="exam_date" id="exam_date" value="{{ old('exam_date') }}" 
                                       class="w-full border rounded px-4 py-2 @error('exam_date') border-red-500 @enderror" required>
                                @error('exam_date')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">Start Time *</label>
                                <input type="time" name="start_time" id="start_time" value="{{ old('start_time') }}" 
                                       class="w-full border rounded px-4 py-2 @error('start_time') border-red-500 @enderror" required>
                                @error('start_time')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">End Time *</label>
                                <input type="time" name="end_time" id="end_time" value="{{ old('end_time') }}" 
                                       class="w-full border rounded px-4 py-2 @error('end_time') border-red-500 @enderror" required>
                                @error('end_time')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="total_marks" class="block text-sm font-medium text-gray-700 mb-2">Total Marks *</label>
                                <input type="number" name="total_marks" id="total_marks" value="{{ old('total_marks') }}" 
                                       class="w-full border rounded px-4 py-2 @error('total_marks') border-red-500 @enderror" 
                                       min="1" required>
                                @error('total_marks')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="venue" class="block text-sm font-medium text-gray-700 mb-2">Venue/Room</label>
                                <input type="text" name="venue" id="venue" value="{{ old('venue') }}" 
                                       class="w-full border rounded px-4 py-2 @error('venue') border-red-500 @enderror" 
                                       placeholder="e.g., Room 301, Lab A">
                                @error('venue')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description/Instructions</label>
                            <textarea name="description" id="description" rows="4" 
                                      class="w-full border rounded px-4 py-2 @error('description') border-red-500 @enderror" 
                                      placeholder="Enter instructions, topics covered, or submission guidelines...">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end gap-4">
                            <a href="{{ route('department-head.exams.index') }}" 
                               class="bg-gray-200 px-6 py-2 rounded hover:bg-gray-300">Cancel</a>
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                                Create Assessment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
