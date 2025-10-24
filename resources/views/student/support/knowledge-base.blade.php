<x-student-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Knowledge Base') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('student.support.knowledge-base') }}" class="flex space-x-4">
                        <div class="flex-1">
                            <input type="text" name="q" value="{{ $query }}" 
                                   placeholder="Search knowledge base articles..."
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <select name="category" class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ $category === request('category') ? 'selected' : '' }}>
                                    {{ ucfirst($category) }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Search
                        </button>
                    </form>
                </div>
            </div>

            <!-- Articles -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($articles->count() > 0)
                        <div class="space-y-6">
                            @foreach($articles as $article)
                                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start mb-3">
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            <a href="{{ route('student.support.article', $article) }}" class="hover:text-blue-600">
                                                {{ $article->title }}
                                            </a>
                                        </h3>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ ucfirst($article->category) }}
                                        </span>
                                    </div>
                                    <p class="text-gray-600 mb-3">{{ Str::limit($article->content, 200) }}</p>
                                    <div class="flex justify-between items-center text-sm text-gray-500">
                                        <span>{{ $article->views }} views</span>
                                        <a href="{{ route('student.support.article', $article) }}" 
                                           class="text-blue-600 hover:text-blue-900 font-medium">
                                            Read More →
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-6">
                            {{ $articles->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No articles found</h3>
                            <p class="mt-1 text-sm text-gray-500">Try adjusting your search terms.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-student-layout>
