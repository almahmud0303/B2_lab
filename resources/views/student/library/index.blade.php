<x-student-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Library Catalog') }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('student.library.my-books') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    My Books
                </a>
                <a href="{{ route('student.library.history') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    History
                </a>
            </div>
        </div>
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

            <!-- Search and Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('student.library.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="md:col-span-2">
                                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Books</label>
                                <input type="text" id="search" name="search" value="{{ request('search') }}"
                                       placeholder="Search by title, author, ISBN, or category..."
                                       class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                <select id="category" name="category" 
                                        class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                                            {{ $category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="availability" class="block text-sm font-medium text-gray-700 mb-1">Availability</label>
                                <select id="availability" name="availability" 
                                        class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Books</option>
                                    <option value="available" {{ request('availability') === 'available' ? 'selected' : '' }}>Available Only</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('student.library.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-6 rounded">
                                Clear Filters
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-6 rounded shadow-sm">
                                <svg class="inline-block w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- My Books Summary -->
            @if($myBooks->count() > 0 || $overdueBooks->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Current Books -->
                    @if($myBooks->count() > 0)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Currently Borrowed ({{ $myBooks->count() }})</h3>
                                <div class="space-y-2">
                                    @foreach($myBooks->take(3) as $bookIssue)
                                        <div class="flex justify-between items-center p-2 bg-blue-50 rounded">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $bookIssue->book->title }}</p>
                                                <p class="text-xs text-gray-500">Due: {{ $bookIssue->due_date->format('M d, Y') }}</p>
                                            </div>
                                            @if($bookIssue->due_date < now())
                                                <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded">Overdue</span>
                                            @endif
                                        </div>
                                    @endforeach
                                    @if($myBooks->count() > 3)
                                        <a href="{{ route('student.library.my-books') }}" class="text-blue-600 hover:text-blue-900 text-sm">
                                            View all {{ $myBooks->count() }} books →
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Overdue Books -->
                    @if($overdueBooks->count() > 0)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-red-900 mb-4">Overdue Books ({{ $overdueBooks->count() }})</h3>
                                <div class="space-y-2">
                                    @foreach($overdueBooks->take(3) as $bookIssue)
                                        <div class="flex justify-between items-center p-2 bg-red-50 rounded">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $bookIssue->book->title }}</p>
                                                <p class="text-xs text-red-600">Overdue since: {{ $bookIssue->due_date->format('M d, Y') }}</p>
                                            </div>
                                            <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded">Overdue</span>
                                        </div>
                                    @endforeach
                                    @if($overdueBooks->count() > 3)
                                        <a href="{{ route('student.library.my-books') }}" class="text-red-600 hover:text-red-900 text-sm">
                                            View all {{ $overdueBooks->count() }} overdue books →
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Book Catalog -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Book Catalog</h3>
                    
                    @if($books->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($books as $book)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-lg transition-all duration-200">
                                    <!-- Book Header -->
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900 text-base mb-1">{{ Str::limit($book->title, 45) }}</h4>
                                            <p class="text-sm text-gray-600">{{ $book->author }}</p>
                                        </div>
                                        <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full {{ $book->available_copies > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $book->available_copies > 0 ? $book->available_copies . ' available' : 'Not Available' }}
                                        </span>
                                    </div>
                                    
                                    <!-- Book Details -->
                                    <div class="space-y-1 mb-4">
                                        <p class="text-xs text-gray-600">
                                            <span class="font-medium">Category:</span> {{ $book->category }}
                                        </p>
                                        <p class="text-xs text-gray-600">
                                            <span class="font-medium">Publisher:</span> {{ $book->publisher }} ({{ $book->publication_year }})
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            <span class="font-medium">ISBN:</span> {{ $book->isbn }}
                                        </p>
                                    </div>
                                    
                                    <!-- Action Buttons -->
                                    <div class="flex gap-2">
                                        <a href="{{ route('student.library.show', $book) }}" 
                                           class="flex-1 bg-blue-500 hover:bg-blue-600 text-white text-center text-xs font-medium py-2 px-3 rounded transition-colors">
                                            View Details
                                        </a>
                                        @if($book->available_copies > 0)
                                            <form method="POST" action="{{ route('student.library.request-book', $book) }}" class="flex-1">
                                                @csrf
                                                <button type="submit" 
                                                        class="w-full bg-green-500 hover:bg-green-600 text-white text-xs font-medium py-2 px-3 rounded transition-colors">
                                                    Issue Book
                                                </button>
                                            </form>
                                        @else
                                            <button disabled 
                                                    class="flex-1 bg-gray-300 text-gray-500 text-xs font-medium py-2 px-3 rounded cursor-not-allowed">
                                                Unavailable
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $books->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No books found</h3>
                            <p class="mt-1 text-sm text-gray-500">Try adjusting your search criteria.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Library Rules -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Library Rules</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Maximum 5 books per student</li>
                                <li>14 days loan period</li>
                                <li>$1 per day fine for overdue books</li>
                                <li>Books cannot be issued if you have overdue books</li>
                            </ul>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('student.library.rules') }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                View complete library rules →
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-6 flex justify-center space-x-4">
                <a href="{{ route('student.dashboard') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</x-student-layout>
