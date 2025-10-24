<x-staff-layout>
    <x-slot name="header">{{ $student->user->name }}</x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-4">
                <h2 class="text-2xl font-bold mb-4">Student Information</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div><strong>Name:</strong> {{ $student->user->name }}</div>
                    <div><strong>Student ID:</strong> {{ $student->student_id }}</div>
                    <div><strong>Email:</strong> {{ $student->user->email }}</div>
                    <div><strong>Phone:</strong> {{ $student->user->phone ?? 'N/A' }}</div>
                    <div><strong>Department:</strong> {{ $student->department->name }}</div>
                    <div><strong>Status:</strong> {{ ucfirst($student->status) }}</div>
                    <div><strong>Admission Date:</strong> {{ $student->admission_date->format('F d, Y') }}</div>
                    <div class="col-span-2">
                        <p class="text-sm text-gray-500 italic">
                            Note: Academic records (courses, results, grades) are restricted to authorized personnel only.
                        </p>
                    </div>
                </div>
            </div>
            <!-- Book Issues Only - Staff cannot view academic records -->
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-xl font-bold mb-4">Book Issues</h3>
                @if($student->bookIssues->count() > 0)
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2">Book Title</th>
                                <th class="text-left py-2">Issue Date</th>
                                <th class="text-left py-2">Due Date</th>
                                <th class="text-left py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($student->bookIssues as $issue)
                                <tr class="border-b">
                                    <td class="py-2">{{ $issue->book->title }}</td>
                                    <td class="py-2">{{ $issue->issue_date?->format('Y-m-d') }}</td>
                                    <td class="py-2">{{ $issue->due_date?->format('Y-m-d') }}</td>
                                    <td class="py-2">
                                        <span class="px-2 py-1 text-xs rounded {{ $issue->status == 'issued' ? 'bg-green-100 text-green-800' : ($issue->status == 'overdue' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                            {{ ucfirst($issue->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-gray-500 text-center py-8">No book issues found.</p>
                @endif
            </div>
        </div>
    </div>
</x-staff-layout>