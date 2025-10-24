<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Students Management') }}
            </h2>
            <a href="{{ route('admin.students.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Add New Student
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Students</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $students->total() }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Active Students</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $students->where('is_active', true)->count() }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">New This Month</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $students->where('created_at', '>=', now()->startOfMonth())->count() }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Inactive</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $students->where('is_active', false)->count() }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Students Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Search and Filter Form -->
                    <form method="GET" action="{{ route('admin.students.index') }}" class="mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <!-- Search Input -->
                            <div class="md:col-span-2">
                                <input type="text" 
                                       name="search" 
                                       value="{{ request('search') }}"
                                       placeholder="Search by name, email, or student ID..." 
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <!-- Department Filter -->
                            <div>
                                <select name="department_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">All Departments</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Year Filter -->
                            <div>
                                <select name="academic_year" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">All Years</option>
                                    <option value="1st" {{ request('academic_year') == '1st' ? 'selected' : '' }}>1st Year</option>
                                    <option value="2nd" {{ request('academic_year') == '2nd' ? 'selected' : '' }}>2nd Year</option>
                                    <option value="3rd" {{ request('academic_year') == '3rd' ? 'selected' : '' }}>3rd Year</option>
                                    <option value="4th" {{ request('academic_year') == '4th' ? 'selected' : '' }}>4th Year</option>
                                </select>
                            </div>

                            <!-- Semester Filter -->
                            <div>
                                <select name="semester" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">All Semesters</option>
                                    <optgroup label="1st Year">
                                        <option value="1st" {{ request('semester') == '1st' ? 'selected' : '' }}>1st Semester</option>
                                        <option value="2nd" {{ request('semester') == '2nd' ? 'selected' : '' }}>2nd Semester</option>
                                    </optgroup>
                                    <optgroup label="2nd Year">
                                        <option value="3rd" {{ request('semester') == '3rd' ? 'selected' : '' }}>3rd Semester</option>
                                        <option value="4th" {{ request('semester') == '4th' ? 'selected' : '' }}>4th Semester</option>
                                    </optgroup>
                                    <optgroup label="3rd Year">
                                        <option value="5th" {{ request('semester') == '5th' ? 'selected' : '' }}>5th Semester</option>
                                        <option value="6th" {{ request('semester') == '6th' ? 'selected' : '' }}>6th Semester</option>
                                    </optgroup>
                                    <optgroup label="4th Year">
                                        <option value="7th" {{ request('semester') == '7th' ? 'selected' : '' }}>7th Semester</option>
                                        <option value="8th" {{ request('semester') == '8th' ? 'selected' : '' }}>8th Semester</option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-4">
                            <div class="flex space-x-2">
                                <!-- Status Filter -->
                                <select name="status" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="graduated" {{ request('status') == 'graduated' ? 'selected' : '' }}>Graduated</option>
                                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                </select>

                                <!-- Active/Inactive Filter -->
                                <select name="is_active" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Login Status</option>
                                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Can Login</option>
                                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Cannot Login</option>
                                </select>
                            </div>

                            <div class="flex space-x-2">
                                <a href="{{ route('admin.students.index') }}" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-md">
                                    Clear Filters
                                </a>
                                <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-700 text-white rounded-md">
                                    Apply Filters
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Results Count -->
                    <div class="mb-4 text-sm text-gray-600">
                        Showing {{ $students->firstItem() ?? 0 }} to {{ $students->lastItem() ?? 0 }} of {{ $students->total() }} students
                        @if(request()->hasAny(['search', 'department_id', 'academic_year', 'semester', 'status', 'is_active']))
                            <span class="font-medium">(filtered)</span>
                        @endif
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semester</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($students as $student)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-blue-600">
                                                            {{ strtoupper(substr($student->user->name, 0, 2)) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $student->user->name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $student->user->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $student->student_id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <span class="font-medium">{{ $student->department->code ?? 'N/A' }}</span>
                                            <div class="text-xs text-gray-500">{{ $student->department->name ?? 'Not Assigned' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $student->academic_year }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $student->semester }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex flex-col space-y-1">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                    {{ $student->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                                    {{ $student->status === 'inactive' ? 'bg-gray-100 text-gray-800' : '' }}
                                                    {{ $student->status === 'graduated' ? 'bg-blue-100 text-blue-800' : '' }}
                                                    {{ $student->status === 'suspended' ? 'bg-red-100 text-red-800' : '' }}">
                                                    {{ ucfirst($student->status) }}
                                                </span>
                                                @if($student->is_active)
                                                    <span class="inline-flex px-2 py-1 text-xs rounded-full bg-green-50 text-green-700">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>
                                                        Login OK
                                                    </span>
                                                @else
                                                    <span class="inline-flex px-2 py-1 text-xs rounded-full bg-red-50 text-red-700">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                        </svg>
                                                        Disabled
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            <a href="{{ route('admin.students.show', $student) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                            <a href="{{ route('admin.students.edit', $student) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                            <form method="POST" action="{{ route('admin.students.destroy', $student) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this student?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                            </svg>
                                            <h3 class="mt-2 text-sm font-medium text-gray-900">No students found</h3>
                                            <p class="mt-1 text-sm text-gray-500">
                                                @if(request()->hasAny(['search', 'department_id', 'academic_year', 'semester', 'status', 'is_active']))
                                                    No students match your search criteria. Try adjusting your filters.
                                                @else
                                                    Get started by creating a new student.
                                                @endif
                                            </p>
                                            @if(request()->hasAny(['search', 'department_id', 'academic_year', 'semester', 'status', 'is_active']))
                                                <div class="mt-6">
                                                    <a href="{{ route('admin.students.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                                        Clear all filters
                                                    </a>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $students->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
