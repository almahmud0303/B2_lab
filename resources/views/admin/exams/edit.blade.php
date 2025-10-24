<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Exam: ' . $exam->title) }}
            </h2>
            <a href="{{ route('admin.exams.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Exams
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.exams.update', $exam) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Exam Information -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900">Exam Information</h3>
                                
                                <div>
                                    <x-input-label for="title" :value="__('Exam Title')" />
                                    <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $exam->title)" required autofocus />
                                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="course_id" :value="__('Course')" />
                                    <select id="course_id" name="course_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                        <option value="">Select Course</option>
                                        @foreach(\App\Models\Course::where('is_active', true)->with('department')->get() as $course)
                                            <option value="{{ $course->id }}" {{ old('course_id', $exam->course_id) == $course->id ? 'selected' : '' }}>
                                                {{ $course->title }} - {{ $course->department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('course_id')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="type" :value="__('Exam Type')" />
                                    <select id="type" name="type" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                        <option value="">Select Type</option>
                                        <option value="quiz" {{ old('type', $exam->type) == 'quiz' ? 'selected' : '' }}>Quiz</option>
                                        <option value="midterm" {{ old('type', $exam->type) == 'midterm' ? 'selected' : '' }}>Midterm</option>
                                        <option value="final" {{ old('type', $exam->type) == 'final' ? 'selected' : '' }}>Final</option>
                                        <option value="assignment" {{ old('type', $exam->type) == 'assignment' ? 'selected' : '' }}>Assignment</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('type')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="total_marks" :value="__('Total Marks')" />
                                    <x-text-input id="total_marks" class="block mt-1 w-full" type="number" name="total_marks" :value="old('total_marks', $exam->total_marks)" min="1" max="100" required />
                                    <x-input-error :messages="$errors->get('total_marks')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="description" :value="__('Description')" />
                                    <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $exam->description) }}</textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>
                            </div>

                            <!-- Schedule Information -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900">Schedule Information</h3>
                                
                                <div>
                                    <x-input-label for="exam_date" :value="__('Exam Date')" />
                                    <x-text-input id="exam_date" class="block mt-1 w-full" type="date" name="exam_date" :value="old('exam_date', $exam->exam_date?->format('Y-m-d'))" required />
                                    <x-input-error :messages="$errors->get('exam_date')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="start_time" :value="__('Start Time')" />
                                    <x-text-input id="start_time" class="block mt-1 w-full" type="time" name="start_time" :value="old('start_time', $exam->start_time?->format('H:i'))" required />
                                    <x-input-error :messages="$errors->get('start_time')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="end_time" :value="__('End Time')" />
                                    <x-text-input id="end_time" class="block mt-1 w-full" type="time" name="end_time" :value="old('end_time', $exam->end_time?->format('H:i'))" required />
                                    <x-input-error :messages="$errors->get('end_time')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="venue" :value="__('Venue/Room')" />
                                    <x-text-input id="venue" class="block mt-1 w-full" type="text" name="venue" :value="old('venue', $exam->venue)" />
                                    <x-input-error :messages="$errors->get('venue')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="status" :value="__('Status')" />
                                    <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                        <option value="scheduled" {{ old('status', $exam->status) == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                        <option value="ongoing" {{ old('status', $exam->status) == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                        <option value="completed" {{ old('status', $exam->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ old('status', $exam->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                                </div>

                                <div class="mt-6 p-4 bg-green-50 rounded-lg">
                                    <h4 class="text-sm font-medium text-green-900 mb-2">Exam Statistics</h4>
                                    <p class="text-sm text-green-700">{{ $exam->results()->count() }} students have taken this exam</p>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.exams.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                                Cancel
                            </a>
                            <x-primary-button>
                                {{ __('Update Exam') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
