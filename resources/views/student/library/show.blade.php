<x-student-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $book->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Book Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Book Cover -->
                        <div class="flex justify-center">
                            @if($book->cover_image)
                                <img src="{{ asset('storage/' . $book->cover_image) }}" 
                                     alt="{{ $book->title }}" 
                                     class="h-64 w-48 object-cover rounded-lg shadow-lg">
                            @else
                                <div class="h-64 w-48 bg-gray-200 rounded-lg shadow-lg flex items-center justify-center">
                                    <div class="text-center text-gray-500">
                                        <svg class="mx-auto h-16 w-16 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                        <p class="text-sm">No Cover Image</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Book Details -->
                        <div class="md:col-span-2">
                            <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $book->title }}</h1>
                            <p class="text-lg text-gray-600 mb-4">by {{ $book->author }}</p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Publisher</h3>
                                    <p class="text-sm text-gray-900">{{ $book->publisher }}</p>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Publication Year</h3>
                                    <p class="text-sm text-gray-900">{{ $book->publication_year }}</p>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">ISBN</h3>
                                    <p class="text-sm text-gray-900">{{ $book->isbn }}</p>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Available Copies</h3>
                                    <p class="text-sm text-gray-900">{{ $book->available_copies }} / {{ $book->total_copies }}</p>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Category</h3>
                                    <p class="text-sm text-gray-900">{{ $book->category ?? 'General' }}</p>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Location</h3>
                                    <p class="text-sm text-gray-900">{{ $book->location ?? 'Main Library' }}</p>
                                </div>
                            </div>

                            @if($book->description)
                            <div class="mb-6">
                                <h3 class="text-sm font-medium text-gray-500 mb-2">Description</h3>
                                <p class="text-sm text-gray-700">{{ $book->description }}</p>
                            </div>
                            @endif

                            <!-- Availability Status -->
                            <div class="mb-6">
                                @if($book->available_copies > 0)
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                        <div class="flex items-center">
                                            <svg class="h-5 w-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span class="text-green-800 font-medium">Available for borrowing</span>
                                        </div>
                                        <p class="text-green-700 text-sm mt-1">
                                            {{ $book->available_copies }} copy(ies) available
                                        </p>
                                    </div>
                                @else
                                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                        <div class="flex items-center">
                                            <svg class="h-5 w-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span class="text-red-800 font-medium">Not available</span>
                                        </div>
                                        <p class="text-red-700 text-sm mt-1">
                                            All copies are currently borrowed
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Borrowing Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Borrowing Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Loan Period</h4>
                            <p class="text-sm text-gray-900">14 days</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Renewal</h4>
                            <p class="text-sm text-gray-900">Up to 2 times (if not requested by others)</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Fine Rate</h4>
                            <p class="text-sm text-gray-900">$1.00 per day for overdue books</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Maximum Books</h4>
                            <p class="text-sm text-gray-900">5 books per student</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center">
                <a href="{{ route('student.library.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Library
                </a>
                
                @if($book->available_copies > 0)
                    <a href="{{ route('student.library.request', $book) }}" 
                       class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Request Book
                    </a>
                @else
                    <button disabled 
                            class="bg-gray-400 text-white font-bold py-2 px-4 rounded cursor-not-allowed">
                        Not Available
                    </button>
                @endif
            </div>
        </div>
    </div>
</x-student-layout>
