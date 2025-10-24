<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Staff Details - {{ $staff->user->name }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h3 class="text-2xl font-bold">{{ $staff->user->name }}</h3>
                        <p class="text-gray-600">{{ $staff->employee_id }}</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.staff.edit', $staff->id) }}" class="bg-blue-600 text-white px-6 py-2 rounded">Edit</a>
                        <a href="{{ route('admin.staff.index') }}" class="bg-gray-200 px-6 py-2 rounded">Back to List</a>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-semibold mb-3">Personal Information</h4>
                        <div class="space-y-2">
                            <p><strong>Email:</strong> {{ $staff->user->email }}</p>
                            <p><strong>Phone:</strong> {{ $staff->user->phone ?? 'N/A' }}</p>
                            <p><strong>Date of Birth:</strong> {{ $staff->user->date_of_birth?->format('F d, Y') ?? 'N/A' }}</p>
                            <p><strong>Gender:</strong> {{ ucfirst($staff->user->gender ?? 'N/A') }}</p>
                            <p><strong>Address:</strong> {{ $staff->user->address ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-3">Employment Information</h4>
                        <div class="space-y-2">
                            <p><strong>Position:</strong> {{ ucfirst(str_replace('_', ' ', $staff->position)) }}</p>
                            <p><strong>Location:</strong> <span class="px-2 py-1 text-xs rounded {{ $staff->location == 'library' ? 'bg-blue-100 text-blue-800' : ($staff->location == 'administration' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800') }}">{{ ucfirst($staff->location) }}</span></p>
                            <p><strong>Department:</strong> {{ $staff->department->name ?? 'N/A' }}</p>
                            <p><strong>Qualification:</strong> {{ $staff->qualification ?? 'N/A' }}</p>
                            <p><strong>Salary:</strong> {{ $staff->salary ? '৳ ' . number_format($staff->salary, 2) : 'N/A' }}</p>
                            <p><strong>Joining Date:</strong> {{ $staff->joining_date->format('F d, Y') }}</p>
                            <p><strong>Employment Type:</strong> {{ ucfirst(str_replace('_', ' ', $staff->employment_type)) }}</p>
                            <p><strong>Status:</strong> <span class="px-2 py-1 text-xs rounded {{ $staff->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $staff->is_active ? 'Active' : 'Inactive' }}</span></p>
                        </div>
                    </div>
                    @if($staff->responsibilities)
                        <div class="col-span-2">
                            <h4 class="font-semibold mb-3">Responsibilities</h4>
                            <p class="text-gray-700">{{ $staff->responsibilities }}</p>
                        </div>
                    @endif
                </div>
            </div>
            @if($staff->bookIssues->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-xl font-bold mb-4">Book Issues Handled (Recent 10)</h3>
                    <table class="min-w-full">
                        <thead><tr class="border-b"><th class="text-left py-2">Student</th><th class="text-left py-2">Book</th><th class="text-left py-2">Issue Date</th><th class="text-left py-2">Status</th></tr></thead>
                        <tbody>
                            @foreach($staff->bookIssues->take(10) as $issue)
                                <tr class="border-b">
                                    <td class="py-2">{{ $issue->student->user->name ?? 'N/A' }}</td>
                                    <td class="py-2">{{ $issue->book->title }}</td>
                                    <td class="py-2">{{ $issue->issue_date?->format('Y-m-d') }}</td>
                                    <td class="py-2">{{ ucfirst($issue->status) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>