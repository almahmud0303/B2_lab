<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Book') }}
            </h2>
            <a href="{{ route('admin.books.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Books
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.books.update', $book) }}" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Title -->
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700">Book Title *</label>
                                <input type="text" name="title" id="title" value="{{ old('title', $book->title) }}" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('title') border-red-500 @enderror" 
                                       required>
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Author -->
                            <div>
                                <label for="author" class="block text-sm font-medium text-gray-700">Author *</label>
                                <input type="text" name="author" id="author" value="{{ old('author', $book->author) }}" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('author') border-red-500 @enderror" 
                                       required>
                                @error('author')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- ISBN -->
                            <div>
                                <label for="isbn" class="block text-sm font-medium text-gray-700">ISBN *</label>
                                <input type="text" name="isbn" id="isbn" value="{{ old('isbn', $book->isbn) }}" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('isbn') border-red-500 @enderror" 
                                       required>
                                @error('isbn')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Publisher -->
                            <div>
                                <label for="publisher" class="block text-sm font-medium text-gray-700">Publisher</label>
                                <input type="text" name="publisher" id="publisher" value="{{ old('publisher', $book->publisher) }}" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('publisher') border-red-500 @enderror">
                                @error('publisher')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Publication Year -->
                            <div>
                                <label for="publication_year" class="block text-sm font-medium text-gray-700">Publication Year</label>
                                <input type="number" name="publication_year" id="publication_year" value="{{ old('publication_year', $book->publication_year) }}" 
                                       min="1800" max="{{ date('Y') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('publication_year') border-red-500 @enderror">
                                @error('publication_year')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Category -->
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                                <select name="category" id="category" 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('category') border-red-500 @enderror">
                                    <option value="">Select Category</option>
                                    <option value="Fiction" {{ old('category', $book->category) == 'Fiction' ? 'selected' : '' }}>Fiction</option>
                                    <option value="Non-Fiction" {{ old('category', $book->category) == 'Non-Fiction' ? 'selected' : '' }}>Non-Fiction</option>
                                    <option value="Textbook" {{ old('category', $book->category) == 'Textbook' ? 'selected' : '' }}>Textbook</option>
                                    <option value="Reference" {{ old('category', $book->category) == 'Reference' ? 'selected' : '' }}>Reference</option>
                                    <option value="Science" {{ old('category', $book->category) == 'Science' ? 'selected' : '' }}>Science</option>
                                    <option value="Technology" {{ old('category', $book->category) == 'Technology' ? 'selected' : '' }}>Technology</option>
                                    <option value="History" {{ old('category', $book->category) == 'History' ? 'selected' : '' }}>History</option>
                                    <option value="Literature" {{ old('category', $book->category) == 'Literature' ? 'selected' : '' }}>Literature</option>
                                </select>
                                @error('category')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Total Copies -->
                            <div>
                                <label for="total_copies" class="block text-sm font-medium text-gray-700">Total Copies *</label>
                                <input type="number" name="total_copies" id="total_copies" value="{{ old('total_copies', $book->total_copies) }}" 
                                       min="1" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('total_copies') border-red-500 @enderror" 
                                       required>
                                @error('total_copies')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Price -->
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                                <input type="number" name="price" id="price" value="{{ old('price', $book->price) }}" 
                                       step="0.01" min="0"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('price') border-red-500 @enderror">
                                @error('price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" id="description" rows="4" 
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('description') border-red-500 @enderror">{{ old('description', $book->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Active Status -->
                        <div>
                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" value="1" 
                                       {{ old('is_active', $book->is_active) ? 'checked' : '' }}
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                    Book is active and available for borrowing
                                </label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('admin.books.index') }}" 
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">
                                Update Book
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
