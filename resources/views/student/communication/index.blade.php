<x-student-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Communication Center') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <a href="{{ route('student.communication.new-conversation') }}" 
                   class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-blue-500 mb-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900">New Message</h3>
                        <p class="text-sm text-gray-600">Send a new message</p>
                    </div>
                </a>

                <a href="{{ route('student.communication.conversations') }}" 
                   class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-green-500 mb-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900">Conversations</h3>
                        <p class="text-sm text-gray-600">View all conversations</p>
                    </div>
                </a>

                <a href="{{ route('student.communication.notifications') }}" 
                   class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6 text-center relative">
                        <svg class="mx-auto h-12 w-12 text-purple-500 mb-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900">Notifications</h3>
                        <p class="text-sm text-gray-600">System notifications</p>
                        @if($unreadNotifications > 0)
                            <span class="absolute top-4 right-4 bg-red-500 text-white text-xs rounded-full h-6 w-6 flex items-center justify-center">
                                {{ $unreadNotifications }}
                            </span>
                        @endif
                    </div>
                </a>

                <a href="{{ route('student.communication.support') }}" 
                   class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-yellow-500 mb-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900">Support</h3>
                        <p class="text-sm text-gray-600">Get help and support</p>
                    </div>
                </a>
            </div>

            <!-- Recent Conversations -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Recent Conversations</h3>
                        <a href="{{ route('student.communication.conversations') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            View All →
                        </a>
                    </div>
                    
                    @if($recentConversations->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentConversations as $conversation)
                                <a href="{{ route('student.communication.conversation', $conversation['user']->id) }}" 
                                   class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                    <div class="flex-shrink-0">
                                        @if($conversation['user']->profile_image)
                                            <img class="h-10 w-10 rounded-full object-cover" 
                                                 src="{{ asset('storage/' . $conversation['user']->profile_image) }}" 
                                                 alt="{{ $conversation['user']->name }}">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <span class="text-gray-600 font-medium text-sm">
                                                    {{ substr($conversation['user']->name, 0, 1) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $conversation['user']->name }}</p>
                                                <p class="text-sm text-gray-600">{{ ucfirst($conversation['user']->role) }}</p>
                                                <p class="text-sm text-gray-500 truncate">{{ $conversation['last_message'] }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-xs text-gray-500">{{ $conversation['last_message_time']->diffForHumans() }}</p>
                                                @if($conversation['unread_count'] > 0)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ $conversation['unread_count'] }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No conversations yet</h3>
                            <p class="mt-1 text-sm text-gray-500">Start a conversation with your teachers or staff.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Contact -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Contact</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <a href="{{ route('student.communication.contact-teachers') }}" 
                           class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Contact Teachers</p>
                                <p class="text-xs text-gray-600">Message your course instructors</p>
                            </div>
                        </a>

                        <a href="{{ route('student.communication.contact-staff') }}" 
                           class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Contact Staff</p>
                                <p class="text-xs text-gray-600">Message administrative staff</p>
                            </div>
                        </a>

                        <a href="{{ route('student.communication.contact-admin') }}" 
                           class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Contact Admin</p>
                                <p class="text-xs text-gray-600">Message administrators</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Notifications -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Recent Notifications</h3>
                        <a href="{{ route('student.communication.notifications') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            View All →
                        </a>
                    </div>
                    
                    @if($notifications->count() > 0)
                        <div class="space-y-3">
                            @foreach($notifications as $notification)
                                <div class="flex items-start p-3 {{ $notification['is_read'] ? 'bg-gray-50' : 'bg-blue-50' }} rounded-lg">
                                    <div class="flex-shrink-0">
                                        @if($notification['type'] === 'result')
                                            <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        @elseif($notification['type'] === 'fee')
                                            <svg class="h-5 w-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
                                            </svg>
                                        @else
                                            <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $notification['title'] }}</p>
                                                <p class="text-sm text-gray-600">{{ $notification['message'] }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-xs text-gray-500">{{ $notification['created_at']->diffForHumans() }}</p>
                                                @if(!$notification['is_read'])
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        New
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No notifications</h3>
                            <p class="mt-1 text-sm text-gray-500">You're all caught up!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-student-layout>
