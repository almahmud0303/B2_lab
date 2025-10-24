<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Create Department Notice') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $departmentHead->department->name ?? 'No Department Assigned' }}
                </p>
            </div>
            <a href="{{ route('department-head.notices.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                Back to Notices
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Department Information -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h4 class="text-sm font-medium text-blue-800">Department Notice</h4>
                        <p class="text-sm text-blue-600">This notice will be visible to all students and teachers in your department: <strong>{{ $departmentHead->department->name ?? 'No Department Assigned' }}</strong></p>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-6">Create Department Notice</h3>
                    
                    <form action="{{ route('department-head.notices.store') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Title -->
                            <div class="md:col-span-2">
                                <label for="title" class="block text-sm font-medium text-gray-700">Title *</label>
                                <input type="text" name="title" id="title" 
                                       value="{{ old('title') }}"
                                       placeholder="Enter notice title"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-300 @enderror" 
                                       required>
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Type -->
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700">Type *</label>
                                <select name="type" id="type" 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('type') border-red-300 @enderror" 
                                        required>
                                    <option value="general" {{ old('type') == 'general' ? 'selected' : '' }}>General</option>
                                    <option value="academic" {{ old('type') == 'academic' ? 'selected' : '' }}>Academic</option>
                                    <option value="exam" {{ old('type') == 'exam' ? 'selected' : '' }}>Exam</option>
                                    <option value="fee" {{ old('type') == 'fee' ? 'selected' : '' }}>Fee</option>
                                    <option value="library" {{ old('type') == 'library' ? 'selected' : '' }}>Library</option>
                                    <option value="event" {{ old('type') == 'event' ? 'selected' : '' }}>Event</option>
                                </select>
                                @error('type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Priority -->
                            <div>
                                <label for="priority" class="block text-sm font-medium text-gray-700">Priority *</label>
                                <select name="priority" id="priority" 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('priority') border-red-300 @enderror" 
                                        required>
                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                                @error('priority')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Target Roles -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Target Roles</label>
                                <div class="space-y-2">
                                    @php
                                        $selectedRoles = old('target_roles', ['student', 'teacher']);
                                    @endphp
                                    <div class="flex items-center">
                                        <input type="checkbox" name="target_roles[]" id="role_student" value="student"
                                               {{ in_array('student', $selectedRoles) ? 'checked' : '' }}
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label for="role_student" class="ml-2 block text-sm text-gray-900">Students</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" name="target_roles[]" id="role_teacher" value="teacher"
                                               {{ in_array('teacher', $selectedRoles) ? 'checked' : '' }}
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label for="role_teacher" class="ml-2 block text-sm text-gray-900">Teachers</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" name="target_roles[]" id="role_staff" value="staff"
                                               {{ in_array('staff', $selectedRoles) ? 'checked' : '' }}
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label for="role_staff" class="ml-2 block text-sm text-gray-900">Staff</label>
                                    </div>
                                </div>
                                @error('target_roles')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Publish Date -->
                            <div>
                                <label for="publish_date" class="block text-sm font-medium text-gray-700">Publish Date *</label>
                                <input type="date" name="publish_date" id="publish_date" 
                                       value="{{ old('publish_date', now()->format('Y-m-d')) }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('publish_date') border-red-300 @enderror" 
                                       required>
                                @error('publish_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Expiry Date -->
                            <div>
                                <label for="expiry_date" class="block text-sm font-medium text-gray-700">Expiry Date</label>
                                <input type="date" name="expiry_date" id="expiry_date" 
                                       value="{{ old('expiry_date') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('expiry_date') border-red-300 @enderror">
                                @error('expiry_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status Checkboxes -->
                            <div class="md:col-span-2">
                                <div class="space-y-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="is_published" id="is_published" value="1" 
                                               {{ old('is_published') ? 'checked' : '' }}
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label for="is_published" class="ml-2 block text-sm text-gray-900">
                                            Published
                                        </label>
                                    </div>

                                    <div class="flex items-center">
                                        <input type="checkbox" name="is_pinned" id="is_pinned" value="1" 
                                               {{ old('is_pinned') ? 'checked' : '' }}
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label for="is_pinned" class="ml-2 block text-sm text-gray-900">
                                            Pinned (Show at top)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-700">Content *</label>
                            <textarea name="content" id="content" rows="10" 
                                      placeholder="Enter notice content..."
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('content') border-red-300 @enderror" 
                                      required>{{ old('content') }}</textarea>
                            @error('content')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('department-head.notices.index') }}" 
                               class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                                Create Notice
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
