<x-student-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Library Search') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Search Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('student.library.search') }}" class="flex space-x-4">
                        <div class="flex-1">
                            <input type="text" name="q" value="{{ $query }}" 
                                   placeholder="Search books by title, author, ISBN, or category..."
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Search
                        </button>
                    </form>
                </div>
            </div>

            <!-- Search Results -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($query)
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            Search Results for "{{ $query }}" ({{ $books->count() }} found)
                        </h3>
                    @else
                        <h3 class="text-lg font-medium text-gray-900 mb-4">All Books</h3>
                    @endif
                    
                    @if($books->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($books as $book)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <h4 class="text-lg font-semibold text-gray-900">{{ $book->title }}</h4>
                                            <p class="text-sm text-gray-600">by {{ $book->author }}</p>
                                        </div>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $book->available_copies > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $book->available_copies > 0 ? 'Available' : 'Unavailable' }}
                                        </span>
                                    </div>
                                    
                                    <div class="space-y-2 mb-4">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">ISBN:</span>
                                            <span class="font-medium">{{ $book->isbn }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Category:</span>
                                            <span class="font-medium">{{ $book->category }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Available:</span>
                                            <span class="font-medium">{{ $book->available_copies }} / {{ $book->total_copies }}</span>
                                        </div>
                                    </div>

                                    <div class="flex space-x-2">
                                        <a href="{{ route('student.library.show', $book) }}" 
                                           class="flex-1 bg-blue-500 hover:bg-blue-700 text-white text-center py-2 px-3 rounded text-sm font-medium">
                                            View Details
                                        </a>
                                        @if($book->available_copies > 0)
                                            <form method="POST" action="{{ route('student.library.request-book', $book) }}" class="flex-1">
                                                @csrf
                                                <button type="submit" class="w-full bg-green-500 hover:bg-green-700 text-white text-center py-2 px-3 rounded text-sm font-medium">
                                                    Request Book
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No books found</h3>
                            <p class="mt-1 text-sm text-gray-500">Try adjusting your search terms.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-student-layout>
