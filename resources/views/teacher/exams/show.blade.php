<x-teacher-layout>
    <x-slot name="header">{{ $exam->title }}</x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white p-6 rounded-lg shadow-sm mb-4">
                <h2 class="text-2xl font-bold mb-4">Assessment Details</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div><strong>Course:</strong> {{ $exam->course->title }}</div>
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
                    <a href="{{ route('teacher.exams.enter-marks', $exam->id) }}" class="bg-green-600 text-white px-6 py-2 rounded">Enter Marks</a>
                    <a href="{{ route('teacher.exams.edit', $exam->id) }}" class="bg-blue-600 text-white px-6 py-2 rounded">Edit</a>
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
</x-teacher-layout>