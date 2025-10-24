<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Notice Details: ' . $notice->title) }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $departmentHead->department->name ?? 'No Department Assigned' }}
                </p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('department-head.notices.edit', $notice) }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Edit Notice
                </a>
                <a href="{{ route('department-head.notices.index') }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                    Back to Notices
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Notice Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-2xl font-semibold text-gray-800">{{ $notice->title }}</h3>
                            <p class="text-gray-600">Created by: {{ $notice->user->name }}</p>
                            <p class="text-gray-600">Published: {{ $notice->publish_date->format('F d, Y') }}</p>
                        </div>
                        <div class="text-right space-y-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                @if($notice->priority === 'urgent') bg-red-100 text-red-800
                                @elseif($notice->priority === 'high') bg-orange-100 text-orange-800
                                @elseif($notice->priority === 'medium') bg-yellow-100 text-yellow-800
                                @else bg-green-100 text-green-800 @endif">
                                {{ ucfirst($notice->priority) }} Priority
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                @if($notice->is_published) bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $notice->is_published ? 'Published' : 'Draft' }}
                            </span>
                            @if($notice->is_pinned)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    Pinned
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Notice Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-2">Notice Information</h4>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Type</dt>
                                    <dd class="text-sm text-gray-900">{{ ucfirst($notice->type) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Target Roles</dt>
                                    <dd class="text-sm text-gray-900">{{ $notice->target_roles_list }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Publish Date</dt>
                                    <dd class="text-sm text-gray-900">{{ $notice->publish_date->format('F d, Y H:i') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Expiry Date</dt>
                                    <dd class="text-sm text-gray-900">{{ $notice->expiry_date ? $notice->expiry_date->format('F d, Y H:i') : 'No expiry' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-800 mb-2">Department Information</h4>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Department</dt>
                                    <dd class="text-sm text-gray-900">{{ $departmentHead->department->name ?? 'Not Assigned' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Created By</dt>
                                    <dd class="text-sm text-gray-900">{{ $notice->user->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Created At</dt>
                                    <dd class="text-sm text-gray-900">{{ $notice->created_at->format('F d, Y H:i') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="text-sm text-gray-900">
                                        @if($notice->is_expired)
                                            <span class="text-red-600">Expired</span>
                                        @elseif($notice->is_published)
                                            <span class="text-green-600">Active</span>
                                        @else
                                            <span class="text-gray-600">Draft</span>
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Notice Content -->
                    <div class="mt-6">
                        <h4 class="font-semibold text-gray-800 mb-2">Content</h4>
                        <div class="prose max-w-none">
                            <div class="text-gray-700 whitespace-pre-wrap">{{ $notice->content }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Actions</h3>
                    <div class="flex space-x-4">
                        <a href="{{ route('department-head.notices.edit', $notice) }}" 
                           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            Edit Notice
                        </a>
                        @if(!$notice->is_published)
                            <form method="POST" action="{{ route('department-head.notices.toggle-status', $notice) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700"
                                        onclick="return confirm('Are you sure you want to publish this notice?')">
                                    Publish Notice
                                </button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('department-head.notices.destroy', $notice) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700"
                                    onclick="return confirm('Are you sure you want to delete this notice?')">
                                Delete Notice
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
