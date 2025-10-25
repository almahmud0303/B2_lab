<x-staff-layout>
    <x-slot name="header">My Profile</x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto">
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif
            <div class="bg-white shadow-sm sm:rounded-lg mb-6 p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-6">
                        <div class="flex-shrink-0">
                            @if($staff->user->profile_image)
                                <img class="h-32 w-32 rounded-full object-cover border-4 border-green-200" src="{{ asset('storage/' . $staff->user->profile_image) }}" alt="{{ $staff->user->name }}">
                            @else
                                <div class="h-32 w-32 rounded-full bg-green-500 flex items-center justify-center text-white text-4xl font-bold border-4 border-green-200">{{ strtoupper(substr($staff->user->name, 0, 2)) }}</div>
                            @endif
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">{{ $staff->user->name }}</h1>
                            <p class="text-lg text-gray-600 mt-1">{{ ucfirst(str_replace('_', ' ', $staff->position)) }}</p>
                            <p class="text-sm text-gray-500 mt-1">Employee ID: {{ $staff->employee_id }}</p>
                            <p class="text-sm text-gray-500">Location: <span class="px-2 py-1 rounded {{ $staff->location == 'library' ? 'bg-blue-100 text-blue-800' : ($staff->location == 'administration' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800') }}">{{ ucfirst($staff->location) }}</span></p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('staff.profile.edit') }}" class="bg-green-600 hover:bg-green-700 text-white text-sm py-1 px-3 rounded">Edit Profile</a>
                        <a href="{{ route('staff.profile.change-password') }}" class="bg-gray-600 hover:bg-gray-700 text-white text-sm py-1 px-3 rounded">Change Password</a>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Contact Information</h2>
                    <div class="space-y-3">
                        <div><label class="text-sm font-medium text-gray-500">Email</label><p class="text-lg text-gray-900">{{ $staff->user->email }}</p></div>
                        <div><label class="text-sm font-medium text-gray-500">Phone</label><p class="text-lg text-gray-900">{{ $staff->user->phone ?? 'Not provided' }}</p></div>
                        <div><label class="text-sm font-medium text-gray-500">Address</label><p class="text-lg text-gray-900">{{ $staff->user->address ?? 'Not provided' }}</p></div>
                    </div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Professional Information</h2>
                    <div class="space-y-3">
                        <div><label class="text-sm font-medium text-gray-500">Position</label><p class="text-lg text-gray-900">{{ ucfirst(str_replace('_', ' ', $staff->position)) }}</p></div>
                        <div><label class="text-sm font-medium text-gray-500">Department</label><p class="text-lg text-gray-900">{{ $staff->department->name ?? 'N/A' }}</p></div>
                        <div><label class="text-sm font-medium text-gray-500">Joining Date</label><p class="text-lg text-gray-900">{{ $staff->joining_date->format('F d, Y') }}</p></div>
                        <div><label class="text-sm font-medium text-gray-500">Employment Type</label><p class="text-lg text-gray-900">{{ ucfirst(str_replace('_', ' ', $staff->employment_type)) }}</p></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-staff-layout>