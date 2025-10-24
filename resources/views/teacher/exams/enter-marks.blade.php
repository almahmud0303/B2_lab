<x-teacher-layout>
    <x-slot name="header">Enter Marks - {{ $exam->exam_name }}</x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <form method="POST" action="{{ route('teacher.exams.save-marks', $exam->id) }}">
                    @csrf
                    <div class="mb-4">
                        <h3 class="text-xl font-bold">{{ $exam->course->title }}</h3>
                        <p class="text-gray-600">Total Marks: {{ $exam->total_marks }}</p>
                    </div>
                    <table class="min-w-full">
                        <thead><tr class="border-b"><th class="text-left py-2">Student Name</th><th class="text-left py-2">Student ID</th><th class="text-left py-2">Marks</th><th class="text-left py-2">Remarks</th></tr></thead>
                        <tbody>
                            @foreach($students as $student)
                                <tr class="border-b">
                                    <td class="py-2">{{ $student->user->name }}</td>
                                    <td class="py-2">{{ $student->student_id }}</td>
                                    <td class="py-2">
                                        <input type="number" name="marks[{{ $student->id }}]" value="{{ $existingResults->get($student->id)?->marks_obtained }}" min="0" max="{{ $exam->total_marks }}" step="0.01" class="border rounded px-2 py-1 w-24">
                                    </td>
                                    <td class="py-2">
                                        <input type="text" name="remarks[{{ $student->id }}]" value="{{ $existingResults->get($student->id)?->remarks }}" class="border rounded px-2 py-1 w-full">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-6 flex justify-end gap-2">
                        <a href="{{ route('teacher.exams.show', $exam->id) }}" class="bg-gray-200 px-6 py-2 rounded">Cancel</a>
                        <button type="submit" name="save" class="bg-blue-600 text-white px-6 py-2 rounded">Save as Draft</button>
                        <button type="submit" name="publish" class="bg-green-600 text-white px-6 py-2 rounded">Save & Publish</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-teacher-layout>