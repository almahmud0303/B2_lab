<x-student-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Notices & Announcements') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('student.dashboard') }}" class="bg-gray-500 hover:bg-gray-700 text-white text-sm py-1 px-3 rounded">
                    Back to Dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Quick Links -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('student.notices.urgent') }}" class="bg-red-50 border border-red-200 rounded-lg p-4 hover:bg-red-100 transition-colors">
                    <div class="flex items-center">
                        <svg class="h-8 w-8 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-900">Urgent Notices</h3>
                            <p class="text-xs text-red-600">Time-sensitive announcements</p>
                        </div>
                    </div>
                </a>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="h-8 w-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7 4a3 3 0 016 0v4a3 3 0 11-6 0V4zm4 10.93A7.001 7.001 0 0017 8a1 1 0 10-2 0A5 5 0 015 8a1 1 0 00-2 0 7.001 7.001 0 006 6.93V17H6a1 1 0 100 2h8a1 1 0 100-2h-3v-2.07z" clip-rule="evenodd"/>
                        </svg>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-900">Total Notices</h3>
                            <p class="text-xs text-blue-600">{{ $notices->total() }} published notices</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="h-8 w-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-green-900">Recent (7 Days)</h3>
                            <p class="text-xs text-green-600">{{ $recentNotices->count() }} new notices</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pinned Notices -->
            @if($pinnedNotices->count() > 0)
                <div class="bg-yellow-50 border-l-4 border-yellow-400 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-yellow-900 mb-4">
                            <svg class="inline-block w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            Pinned Notices
                        </h3>
                        <div class="space-y-3">
                            @foreach($pinnedNotices as $notice)
                                <a href="{{ route('student.notices.show', $notice) }}" class="block bg-white p-4 rounded-lg hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900">{{ $notice->title }}</h4>
                                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($notice->content, 150) }}</p>
                                            <div class="flex items-center space-x-3 mt-2">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $notice->priority === 'urgent' ? 'bg-red-100 text-red-800' : ($notice->priority === 'high' ? 'bg-orange-100 text-orange-800' : ($notice->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800')) }}">
                                                    {{ ucfirst($notice->priority) }}
                                                </span>
                                                <span class="text-xs text-gray-500">{{ $notice->created_at->format('M d, Y') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- All Notices -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">All Notices</h3>
                    
                    @if($notices->count() > 0)
                        <div class="space-y-4">
                            @foreach($notices as $notice)
                                <a href="{{ route('student.notices.show', $notice) }}" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900">{{ $notice->title }}</h4>
                                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($notice->content, 150) }}</p>
                                            <div class="flex items-center space-x-3 mt-3">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    {{ ucfirst($notice->type) }}
                                                </span>
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $notice->priority === 'urgent' ? 'bg-red-100 text-red-800' : ($notice->priority === 'high' ? 'bg-orange-100 text-orange-800' : ($notice->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800')) }}">
                                                    {{ ucfirst($notice->priority) }}
                                                </span>
                                                <span class="text-xs text-gray-500">{{ $notice->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $notices->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No notices found</h3>
                            <p class="mt-1 text-sm text-gray-500">There are no published notices at the moment.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-student-layout>
