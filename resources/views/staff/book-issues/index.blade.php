<x-staff-layout>
    <x-slot name="header">Book Issues</x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif
            <div class="mb-4 flex justify-between">
                <form method="GET" class="flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search student or book..." class="border rounded px-4 py-2 w-64">
                    <select name="status" class="border rounded px-4 py-2">
                        <option value="">All Status</option>
                        <option value="requested" {{ request('status') == 'requested' ? 'selected' : '' }}>Requested</option>
                        <option value="issued" {{ request('status') == 'issued' ? 'selected' : '' }}>Issued</option>
                        <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                        <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                    </select>
                    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded">Search</button>
                </form>
                <a href="{{ route('staff.book-issues.create') }}" class="bg-blue-600 text-white px-6 py-2 rounded">Issue New Book</a>
            </div>
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <table class="min-w-full">
                    <thead><tr class="border-b"><th class="text-left py-2">Student</th><th class="text-left py-2">Book</th><th class="text-left py-2">Issue Date</th><th class="text-left py-2">Due Date</th><th class="text-left py-2">Status</th><th class="text-left py-2">Actions</th></tr></thead>
                    <tbody>
                        @foreach($bookIssues as $issue)
                            <tr class="border-b">
                                <td class="py-2">{{ $issue->student->user->name }}<br><span class="text-xs text-gray-500">{{ $issue->student->student_id }}</span></td>
                                <td class="py-2">{{ Str::limit($issue->book->title, 40) }}</td>
                                <td class="py-2">{{ $issue->issue_date?->format('Y-m-d') }}</td>
                                <td class="py-2">{{ $issue->due_date?->format('Y-m-d') }}</td>
                                <td class="py-2"><span class="px-2 py-1 text-xs rounded {{ $issue->status == 'issued' ? 'bg-green-100 text-green-800' : ($issue->status == 'overdue' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">{{ ucfirst($issue->status) }}</span></td>
                                <td class="py-2">
                                    <a href="{{ route('staff.book-issues.show', $issue->id) }}" class="text-blue-600">View</a>
                                    @if($issue->status == 'requested')
                                        <form method="POST" action="{{ route('staff.book-issues.approve', $issue->id) }}" class="inline ml-2">@csrf<button class="text-green-600">Approve</button></form>
                                    @elseif(in_array($issue->status, ['issued', 'overdue']))
                                        <form method="POST" action="{{ route('staff.book-issues.return', $issue->id) }}" class="inline ml-2">@csrf<button class="text-green-600">Return</button></form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">{{ $bookIssues->links() }}</div>
            </div>
        </div>
    </div>
</x-staff-layout>