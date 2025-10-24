<x-staff-layout>
    <x-slot name="header">Book Issue Details</x-slot>
    <div class="py-6">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h2 class="text-2xl font-bold mb-4">Issue Information</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div><strong>Student:</strong> {{ $bookIssue->student->user->name }}</div>
                    <div><strong>Student ID:</strong> {{ $bookIssue->student->student_id }}</div>
                    <div><strong>Book:</strong> {{ $bookIssue->book->title }}</div>
                    <div><strong>ISBN:</strong> {{ $bookIssue->book->isbn }}</div>
                    <div><strong>Issue Date:</strong> {{ $bookIssue->issue_date?->format('F d, Y') }}</div>
                    <div><strong>Due Date:</strong> {{ $bookIssue->due_date?->format('F d, Y') }}</div>
                    <div><strong>Return Date:</strong> {{ $bookIssue->return_date?->format('F d, Y') ?? 'Not returned' }}</div>
                    <div><strong>Status:</strong> <span class="px-2 py-1 text-xs rounded {{ $bookIssue->status == 'issued' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">{{ ucfirst($bookIssue->status) }}</span></div>
                    @if($bookIssue->staff)
                        <div><strong>Issued By:</strong> {{ $bookIssue->staff->user->name }}</div>
                    @endif
                    @if($bookIssue->notes)
                        <div class="col-span-2"><strong>Notes:</strong><p class="mt-2">{{ $bookIssue->notes }}</p></div>
                    @endif
                </div>
                <div class="mt-6 flex gap-2">
                    @if($bookIssue->status == 'requested')
                        <form method="POST" action="{{ route('staff.book-issues.approve', $bookIssue->id) }}" class="inline">@csrf<button class="bg-green-600 text-white px-4 py-2 rounded">Approve</button></form>
                        <form method="POST" action="{{ route('staff.book-issues.reject', $bookIssue->id) }}" class="inline">@csrf<button class="bg-red-600 text-white px-4 py-2 rounded">Reject</button></form>
                    @elseif(in_array($bookIssue->status, ['issued', 'overdue']))
                        <form method="POST" action="{{ route('staff.book-issues.return', $bookIssue->id) }}" class="inline">@csrf<button class="bg-green-600 text-white px-4 py-2 rounded">Mark as Returned</button></form>
                    @endif
                    <a href="{{ route('staff.book-issues.index') }}" class="bg-gray-200 px-4 py-2 rounded">Back to List</a>
                </div>
            </div>
        </div>
    </div>
</x-staff-layout>