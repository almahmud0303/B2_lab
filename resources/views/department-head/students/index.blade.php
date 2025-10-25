<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Department Students') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $department->name ?? 'No Department Assigned' }}
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('department-head.students.search') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Search Students
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                    <p class="text-blue-100 text-sm">Total Students</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['total_students'] }}</p>
                </div>
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
                    <p class="text-green-100 text-sm">1st Year</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['first_year'] }}</p>
                </div>
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                    <p class="text-purple-100 text-sm">2nd Year</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['second_year'] }}</p>
                </div>
                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
                    <p class="text-orange-100 text-sm">3rd Year</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['third_year'] }}</p>
                </div>
                <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-lg p-6 text-white">
                    <p class="text-red-100 text-sm">4th Year</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['fourth_year'] }}</p>
                </div>
            </div>

            <!-- Search Form -->
            <div class="bg-white rounded-lg shadow-sm mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('department-head.students.search') }}" class="flex gap-4">
                        <div class="flex-1">
                            <input type="text" name="query" value="{{ $query ?? '' }}" 
                                   placeholder="Search by name, student ID, or email..." 
                                   class="w-full border rounded px-4 py-2">
                        </div>
                        <div>
                            <select name="year" class="border rounded px-4 py-2">
                                <option value="">All Years</option>
                                <option value="1" {{ ($year ?? '') == '1' ? 'selected' : '' }}>1st Year</option>
                                <option value="2" {{ ($year ?? '') == '2' ? 'selected' : '' }}>2nd Year</option>
                                <option value="3" {{ ($year ?? '') == '3' ? 'selected' : '' }}>3rd Year</option>
                                <option value="4" {{ ($year ?? '') == '4' ? 'selected' : '' }}>4th Year</option>
                            </select>
                        </div>
                        <div>
                            <select name="semester" class="border rounded px-4 py-2">
                                <option value="">All Semesters</option>
                                <option value="1" {{ ($semester ?? '') == '1' ? 'selected' : '' }}>Semester 1</option>
                                <option value="2" {{ ($semester ?? '') == '2' ? 'selected' : '' }}>Semester 2</option>
                            </select>
                        </div>
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                            Search
                        </button>
                    </form>
                </div>
            </div>

            @if($students->count() > 0)
                <!-- Students Table -->
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="px-6 py-4 border-b">
                        <h3 class="text-lg font-semibold">Students List</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Academic Year</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semester</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($students as $student)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-blue-600">
                                                            {{ substr($student->user->name, 0, 2) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $student->user->name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $student->student_id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            Year {{ $student->academic_year }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            Semester {{ $student->semester }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $student->user->email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                {{ $student->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $student->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex gap-2">
                                                <a href="{{ route('department-head.students.show', $student) }}" 
                                                   class="text-blue-600 hover:text-blue-900">View</a>
                                                <a href="{{ route('department-head.students.academic-record', $student) }}" 
                                                   class="text-green-600 hover:text-green-900">Academic Record</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="px-6 py-4 border-t">
                        {{ $students->links() }}
                    </div>
                </div>
            @else
                <div class="bg-white p-12 rounded-lg shadow-sm text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">No Students Found</h3>
                    <p class="text-gray-500">No students match your search criteria.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
