<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $exam->title }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $department->name ?? 'No Department Assigned' }}
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('department-head.exams.edit', $exam->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Edit Assessment
                </a>
                <a href="{{ route('department-head.exams.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                    Back to Assessments
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow-sm mb-4">
                <h2 class="text-2xl font-bold mb-4">Assessment Details</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div><strong>Course:</strong> {{ $exam->course->title }}</div>
                    <div><strong>Teacher:</strong> {{ $exam->course->teacher->user->name ?? 'Not Assigned' }}</div>
                    <div><strong>Date:</strong> {{ $exam->exam_date->format('F d, Y') }}</div>
                    <div><strong>Time:</strong> {{ \Carbon\Carbon::parse($exam->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($exam->end_time)->format('g:i A') }}</div>
                    <div><strong>Type:</strong> {{ ucfirst($exam->type) }}</div>
                    <div><strong>Total Marks:</strong> {{ $exam->total_marks }}</div>
                    <div><strong>Venue:</strong> {{ $exam->venue ?? 'N/A' }}</div>
                    <div><strong>Status:</strong> {{ ucfirst($exam->status) }}</div>
                </div>
                @if($exam->description)
                    <div class="mt-4">
                        <strong>Description:</strong>
                        <p class="mt-2">{{ $exam->description }}</p>
                    </div>
                @endif
                <div class="mt-6 flex gap-2">
                    <a href="{{ route('department-head.exams.enter-marks', $exam->id) }}" class="bg-green-600 text-white px-6 py-2 rounded">Enter Marks</a>
                    <a href="{{ route('department-head.exams.edit', $exam->id) }}" class="bg-blue-600 text-white px-6 py-2 rounded">Edit</a>
                </div>
            </div>
            @if($exam->results->count() > 0)
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-xl font-bold mb-4">Results</h3>
                    <table class="min-w-full">
                        <thead><tr class="border-b"><th class="text-left py-2">Student</th><th class="text-left py-2">Marks</th><th class="text-left py-2">Grade</th><th class="text-left py-2">Status</th></tr></thead>
                        <tbody>
                            @foreach($exam->results as $result)
                                <tr class="border-b">
                                    <td class="py-2">{{ $result->student->user->name }}</td>
                                    <td class="py-2">{{ $result->marks_obtained }}/{{ $result->total_marks }}</td>
                                    <td class="py-2">{{ $result->grade }}</td>
                                    <td class="py-2">{{ $result->is_published ? 'Published' : 'Draft' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
