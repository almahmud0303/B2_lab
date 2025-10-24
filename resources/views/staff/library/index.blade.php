<x-staff-layout>
    <x-slot name="header">Library Management</x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif
            <div class="mb-4 flex justify-between">
                <form method="GET" class="flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search books..." class="border rounded px-4 py-2 w-64">
                    <select name="category" class="border rounded px-4 py-2">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                    <select name="availability" class="border rounded px-4 py-2">
                        <option value="">All Books</option>
                        <option value="available" {{ request('availability') == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="unavailable" {{ request('availability') == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                    </select>
                    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded">Search</button>
                </form>
                <a href="{{ route('staff.library.create') }}" class="bg-blue-600 text-white px-6 py-2 rounded">Add New Book</a>
            </div>
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <table class="min-w-full">
                    <thead><tr class="border-b"><th class="text-left py-2">Title</th><th class="text-left py-2">Author</th><th class="text-left py-2">ISBN</th><th class="text-left py-2">Category</th><th class="text-left py-2">Available</th><th class="text-left py-2">Actions</th></tr></thead>
                    <tbody>
                        @foreach($books as $book)
                            <tr class="border-b">
                                <td class="py-2">{{ $book->title }}</td>
                                <td class="py-2">{{ $book->author }}</td>
                                <td class="py-2">{{ $book->isbn }}</td>
                                <td class="py-2">{{ $book->category }}</td>
                                <td class="py-2">{{ $book->available_copies }}/{{ $book->total_copies }}</td>
                                <td class="py-2">
                                    <a href="{{ route('staff.library.show', $book->id) }}" class="text-blue-600">View</a>
                                    <a href="{{ route('staff.library.edit', $book->id) }}" class="text-green-600 ml-2">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">{{ $books->links() }}</div>
            </div>
        </div>
    </div>
</x-staff-layout>