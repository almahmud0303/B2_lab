<x-staff-layout>
    <x-slot name="header">{{ $book->title }}</x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-4">
                <div class="flex justify-between mb-4">
                    <h2 class="text-2xl font-bold">Book Details</h2>
                    <div class="flex gap-2">
                        <a href="{{ route('staff.library.edit', $book->id) }}" class="bg-green-600 text-white px-4 py-2 rounded">Edit</a>
                        <form method="POST" action="{{ route('staff.library.destroy', $book->id) }}" class="inline" onsubmit="return confirm('Are you sure?')">@csrf @method('DELETE')<button class="bg-red-600 text-white px-4 py-2 rounded">Delete</button></form>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><strong>Title:</strong> {{ $book->title }}</div>
                    <div><strong>Author:</strong> {{ $book->author }}</div>
                    <div><strong>ISBN:</strong> {{ $book->isbn }}</div>
                    <div><strong>Publisher:</strong> {{ $book->publisher ?? 'N/A' }}</div>
                    <div><strong>Year:</strong> {{ $book->publication_year ?? 'N/A' }}</div>
                    <div><strong>Category:</strong> {{ $book->category }}</div>
                    <div><strong>Total Copies:</strong> {{ $book->total_copies }}</div>
                    <div><strong>Available:</strong> {{ $book->available_copies }}</div>
                    <div><strong>Shelf:</strong> {{ $book->shelf_location ?? 'N/A' }}</div>
                    <div class="col-span-2"><strong>Description:</strong><p class="mt-2">{{ $book->description ?? 'No description' }}</p></div>
                </div>
            </div>
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-xl font-bold mb-4">Issue History</h3>
                @if($book->bookIssues->count() > 0)
                    <table class="min-w-full">
                        <thead><tr class="border-b"><th class="text-left py-2">Student</th><th class="text-left py-2">Issue Date</th><th class="text-left py-2">Due Date</th><th class="text-left py-2">Status</th></tr></thead>
                        <tbody>
                            @foreach($book->bookIssues as $issue)
                                <tr class="border-b">
                                    <td class="py-2">{{ $issue->student->user->name }}</td>
                                    <td class="py-2">{{ $issue->issue_date?->format('Y-m-d') }}</td>
                                    <td class="py-2">{{ $issue->due_date?->format('Y-m-d') }}</td>
                                    <td class="py-2">{{ ucfirst($issue->status) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-gray-500">No issue history</p>
                @endif
            </div>
        </div>
    </div>
</x-staff-layout>