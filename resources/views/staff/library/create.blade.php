<x-staff-layout>
    <x-slot name="header">Add New Book</x-slot>
    <div class="py-6">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('staff.library.store') }}">
                    @csrf
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div><label class="block text-sm font-medium text-gray-700 mb-2">Title</label><input type="text" name="title" value="{{ old('title') }}" class="w-full border rounded px-4 py-2" required>@error('title')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror</div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-2">Author</label><input type="text" name="author" value="{{ old('author') }}" class="w-full border rounded px-4 py-2" required></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-2">ISBN</label><input type="text" name="isbn" value="{{ old('isbn') }}" class="w-full border rounded px-4 py-2" required></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-2">Publisher</label><input type="text" name="publisher" value="{{ old('publisher') }}" class="w-full border rounded px-4 py-2"></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-2">Publication Year</label><input type="number" name="publication_year" value="{{ old('publication_year') }}" class="w-full border rounded px-4 py-2"></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-2">Category</label><input type="text" name="category" value="{{ old('category') }}" class="w-full border rounded px-4 py-2" required></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-2">Total Copies</label><input type="number" name="total_copies" value="{{ old('total_copies') }}" class="w-full border rounded px-4 py-2" required></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-2">Available Copies</label><input type="number" name="available_copies" value="{{ old('available_copies') }}" class="w-full border rounded px-4 py-2" required></div>
                        <div class="col-span-2"><label class="block text-sm font-medium text-gray-700 mb-2">Shelf Location</label><input type="text" name="shelf_location" value="{{ old('shelf_location') }}" class="w-full border rounded px-4 py-2"></div>
                        <div class="col-span-2"><label class="block text-sm font-medium text-gray-700 mb-2">Description</label><textarea name="description" rows="3" class="w-full border rounded px-4 py-2">{{ old('description') }}</textarea></div>
                    </div>
                    <div class="flex justify-end gap-2">
                        <a href="{{ route('staff.library.index') }}" class="bg-gray-200 px-6 py-2 rounded">Cancel</a>
                        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded">Add Book</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-staff-layout>