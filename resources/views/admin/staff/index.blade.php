<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Staff Management</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between mb-6">
                        <h3 class="text-2xl font-bold">Staff Members</h3>
                        <a href="{{ route('admin.staff.create') }}" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Add New Staff</a>
                    </div>
                    <form method="GET" class="mb-6">
                        <div class="grid grid-cols-5 gap-4">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, or ID..." class="border rounded px-4 py-2">
                            <select name="position" class="border rounded px-4 py-2">
                                <option value="">All Positions</option>
                                <option value="librarian" {{ request('position') == 'librarian' ? 'selected' : '' }}>Librarian</option>
                                <option value="clerk" {{ request('position') == 'clerk' ? 'selected' : '' }}>Clerk</option>
                                <option value="accountant" {{ request('position') == 'accountant' ? 'selected' : '' }}>Accountant</option>
                                <option value="lab_assistant" {{ request('position') == 'lab_assistant' ? 'selected' : '' }}>Lab Assistant</option>
                                <option value="office_assistant" {{ request('position') == 'office_assistant' ? 'selected' : '' }}>Office Assistant</option>
                                <option value="other" {{ request('position') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            <select name="location" class="border rounded px-4 py-2">
                                <option value="">All Locations</option>
                                <option value="library" {{ request('location') == 'library' ? 'selected' : '' }}>Library</option>
                                <option value="administration" {{ request('location') == 'administration' ? 'selected' : '' }}>Administration</option>
                                <option value="department" {{ request('location') == 'department' ? 'selected' : '' }}>Department</option>
                            </select>
                            <select name="department_id" class="border rounded px-4 py-2">
                                <option value="">All Departments</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->code }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Filter</button>
                        </div>
                    </form>
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2">Name</th>
                                <th class="text-left py-2">Employee ID</th>
                                <th class="text-left py-2">Position</th>
                                <th class="text-left py-2">Location</th>
                                <th class="text-left py-2">Department</th>
                                <th class="text-left py-2">Status</th>
                                <th class="text-left py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($staff as $member)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-3">{{ $member->user->name }}</td>
                                    <td class="py-3">{{ $member->employee_id }}</td>
                                    <td class="py-3">{{ ucfirst(str_replace('_', ' ', $member->position)) }}</td>
                                    <td class="py-3">
                                        <span class="px-2 py-1 text-xs rounded {{ $member->location == 'library' ? 'bg-blue-100 text-blue-800' : ($member->location == 'administration' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800') }}">
                                            {{ ucfirst($member->location) }}
                                        </span>
                                    </td>
                                    <td class="py-3">{{ $member->department->code ?? 'N/A' }}</td>
                                    <td class="py-3">
                                        <span class="px-2 py-1 text-xs rounded {{ $member->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $member->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <a href="{{ route('admin.staff.show', $member->id) }}" class="text-blue-600 hover:underline mr-2">View</a>
                                        <a href="{{ route('admin.staff.edit', $member->id) }}" class="text-green-600 hover:underline mr-2">Edit</a>
                                        <form method="POST" action="{{ route('admin.staff.destroy', $member->id) }}" class="inline" onsubmit="return confirm('Delete this staff member?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $staff->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>