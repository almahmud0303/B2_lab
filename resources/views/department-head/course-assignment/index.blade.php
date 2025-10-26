<x-teacher-layout>
    <x-slot name="header">
        Course Assignment - {{ $department->name }}
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <form method="GET" action="{{ route('department-head.course-assignment.index') }}" class="flex gap-4">
                    <select name="academic_year" class="border rounded px-4 py-2" onchange="this.form.submit()">
                        <option value="">All Years</option>
                        <option value="1st" {{ request('academic_year') == '1st' ? 'selected' : '' }}>1st Year</option>
                        <option value="2nd" {{ request('academic_year') == '2nd' ? 'selected' : '' }}>2nd Year</option>
                        <option value="3rd" {{ request('academic_year') == '3rd' ? 'selected' : '' }}>3rd Year</option>
                        <option value="4th" {{ request('academic_year') == '4th' ? 'selected' : '' }}>4th Year</option>
                    </select>
                    <select name="semester" class="border rounded px-4 py-2" onchange="this.form.submit()">
                        <option value="">All Semesters</option>
                        <option value="1st" {{ request('semester') == '1st' ? 'selected' : '' }}>1st Semester</option>
                        <option value="2nd" {{ request('semester') == '2nd' ? 'selected' : '' }}>2nd Semester</option>
                        <option value="3rd" {{ request('semester') == '3rd' ? 'selected' : '' }}>3rd Semester</option>
                        <option value="4th" {{ request('semester') == '4th' ? 'selected' : '' }}>4th Semester</option>
                        <option value="5th" {{ request('semester') == '5th' ? 'selected' : '' }}>5th Semester</option>
                        <option value="6th" {{ request('semester') == '6th' ? 'selected' : '' }}>6th Semester</option>
                        <option value="7th" {{ request('semester') == '7th' ? 'selected' : '' }}>7th Semester</option>
                        <option value="8th" {{ request('semester') == '8th' ? 'selected' : '' }}>8th Semester</option>
                    </select>
                    <select name="status" class="border rounded px-4 py-2" onchange="this.form.submit()">
                        <option value="">All Courses</option>
                        <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                        <option value="unassigned" {{ request('status') == 'unassigned' ? 'selected' : '' }}>Unassigned</option>
                    </select>
                    <a href="{{ route('department-head.course-assignment.index') }}" class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700">Clear Filters</a>
                    <a href="{{ route('department-head.course-assignment.workload-report') }}" class="bg-purple-600 text-white px-6 py-2 rounded hover:bg-purple-700">Workload Report</a>
                </form>
            </div>

            <!-- Courses List -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b">
                    <h2 class="text-lg font-semibold">Department Courses</h2>
                </div>
                <div class="p-6">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2">Course</th>
                                <th class="text-left py-2">Code</th>
                                <th class="text-left py-2">Year/Sem</th>
                                <th class="text-left py-2">Credits</th>
                                <th class="text-left py-2">Assigned Teacher</th>
                                <th class="text-left py-2">Enrolled</th>
                                <th class="text-left py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($courses as $course)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-3">{{ $course->title }}</td>
                                    <td class="py-3">{{ $course->course_code }}</td>
                                    <td class="py-3">{{ $course->academic_year }}/{{ $course->semester }}</td>
                                    <td class="py-3">{{ $course->credits }}</td>
                                    <td class="py-3">
                                        @if($course->teacher)
                                            {{ $course->teacher->user->name }}
                                        @else
                                            <span class="text-red-600">Unassigned</span>
                                        @endif
                                    </td>
                                    <td class="py-3">{{ $course->enrollments_count }}</td>
                                    <td class="py-3">
                                        <a href="{{ route('department-head.course-assignment.assign', $course->id) }}" 
                                           class="text-blue-600 hover:underline">
                                            {{ $course->teacher ? 'Reassign' : 'Assign' }}
                                        </a>
                                        @if($course->teacher)
                                            <form method="POST" action="{{ route('department-head.course-assignment.unassign', $course->id) }}" class="inline ml-2">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Unassign this teacher?')">
                                                    Unassign
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $courses->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-teacher-layout>

