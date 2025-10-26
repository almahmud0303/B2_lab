<x-student-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Notice Details') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('student.notices.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white text-sm py-1 px-3 rounded">
                    Back to Notices
                </a>
                <a href="{{ route('student.dashboard') }}" class="bg-blue-500 hover:bg-blue-700 text-white text-sm py-1 px-3 rounded">
                    Dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Notice Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Header -->
                    <div class="mb-6">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <h1 class="text-2xl font-bold text-gray-900">{{ $notice->title }}</h1>
                                <div class="flex items-center space-x-3 mt-2">
                                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ ucfirst($notice->type) }}
                                    </span>
                                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $notice->priority === 'urgent' ? 'bg-red-100 text-red-800' : ($notice->priority === 'high' ? 'bg-orange-100 text-orange-800' : ($notice->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800')) }}">
                                        {{ ucfirst($notice->priority) }}
                                    </span>
                                    @if($notice->is_pinned)
                                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Pinned
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Published: {{ $notice->publish_date->format('F d, Y') }}</span>
                            <span>•</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>{{ $notice->created_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="prose max-w-none">
                        <div class="text-gray-700 whitespace-pre-wrap">{{ $notice->content }}</div>
                    </div>

                    <!-- Metadata -->
                    @if($notice->expiry_date)
                        <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-sm text-yellow-800">
                                <strong>Expires:</strong> {{ $notice->expiry_date->format('F d, Y \a\t g:i A') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Related Notices -->
            @if($relatedNotices->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Related Notices</h3>
                        <div class="space-y-3">
                            @foreach($relatedNotices as $related)
                                <a href="{{ route('student.notices.show', $related) }}" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                    <h4 class="font-semibold text-gray-900">{{ $related->title }}</h4>
                                    <p class="text-sm text-gray-600 mt-1">{{ Str::limit($related->content, 100) }}</p>
                                    <div class="flex items-center space-x-2 mt-2">
                                        <span class="text-xs text-gray-500">{{ $related->created_at->diffForHumans() }}</span>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ ucfirst($related->type) }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-student-layout>
