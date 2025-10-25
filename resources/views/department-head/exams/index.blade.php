<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Department Assessments') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $department->name ?? 'No Department Assigned' }}
                </p>
            </div>
            <a href="{{ route('department-head.exams.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Create New Assessment
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($exams->count() > 0)
                <div class="space-y-4">
                    @foreach($exams as $exam)
                        <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 
                            {{ $exam->type === 'quiz' ? 'border-purple-500' : ($exam->type === 'assignment' ? 'border-green-500' : ($exam->type === 'final' ? 'border-red-500' : 'border-blue-500')) }}">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h3 class="text-xl font-bold text-gray-800">{{ $exam->title }}</h3>
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                                            {{ $exam->type === 'quiz' ? 'bg-purple-100 text-purple-800' : '' }}
                                            {{ $exam->type === 'midterm' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $exam->type === 'final' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $exam->type === 'assignment' ? 'bg-green-100 text-green-800' : '' }}">
                                            {{ ucfirst($exam->type) }}
                                        </span>
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                                            {{ $exam->status === 'scheduled' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $exam->status === 'ongoing' ? 'bg-orange-100 text-orange-800' : '' }}
                                            {{ $exam->status === 'completed' ? 'bg-gray-100 text-gray-800' : '' }}
                                            {{ $exam->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ ucfirst($exam->status) }}
                                        </span>
                                    </div>
                                    <p class="text-gray-700 font-medium">{{ $exam->course->title }} ({{ $exam->course->course_code }})</p>
                                    <p class="text-sm text-gray-600">Teacher: {{ $exam->course->teacher->user->name ?? 'Not Assigned' }}</p>
                                    <div class="flex gap-6 mt-2 text-sm text-gray-600">
                                        <span>📅 {{ $exam->exam_date->format('F d, Y') }}</span>
                                        <span>🕐 {{ \Carbon\Carbon::parse($exam->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($exam->end_time)->format('g:i A') }}</span>
                                        <span>📊 Total: {{ $exam->total_marks }} marks</span>
                                        @if($exam->venue)
                                            <span>📍 {{ $exam->venue }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 flex gap-2">
                                <a href="{{ route('department-head.exams.show', $exam->id) }}" 
                                   class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View Details
                                </a>
                                <a href="{{ route('department-head.exams.enter-marks', $exam->id) }}" 
                                   class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Enter Marks
                                </a>
                                <a href="{{ route('department-head.exams.edit', $exam->id) }}" 
                                   class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 text-sm flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                    Edit
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $exams->links() }}
                </div>
            @else
                <div class="bg-white p-12 rounded-lg shadow-sm text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">No Assessments Yet</h3>
                    <p class="text-gray-500 mb-4">Create assessments for courses in your department</p>
                    <a href="{{ route('department-head.exams.create') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                        Create Now
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
