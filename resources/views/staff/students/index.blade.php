<x-staff-layout>
    <x-slot name="header">Student Records</x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto">
            <div class="mb-4">
                <form method="GET" class="flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search students..." class="border rounded px-4 py-2 w-64">
                    <select name="department_id" class="border rounded px-4 py-2">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                    <select name="academic_year" class="border rounded px-4 py-2">
                        <option value="">All Years</option>
                        <option value="1st" {{ request('academic_year') == '1st' ? 'selected' : '' }}>1st Year</option>
                        <option value="2nd" {{ request('academic_year') == '2nd' ? 'selected' : '' }}>2nd Year</option>
                        <option value="3rd" {{ request('academic_year') == '3rd' ? 'selected' : '' }}>3rd Year</option>
                        <option value="4th" {{ request('academic_year') == '4th' ? 'selected' : '' }}>4th Year</option>
                    </select>
                    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded">Search</button>
                </form>
            </div>
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <table class="min-w-full">
                    <thead><tr class="border-b"><th class="text-left py-2">Name</th><th class="text-left py-2">Student ID</th><th class="text-left py-2">Department</th><th class="text-left py-2">Year</th><th class="text-left py-2">Status</th><th class="text-left py-2">Actions</th></tr></thead>
                    <tbody>
                        @foreach($students as $student)
                            <tr class="border-b">
                                <td class="py-2">{{ $student->user->name }}</td>
                                <td class="py-2">{{ $student->student_id }}</td>
                                <td class="py-2">{{ $student->department->code }}</td>
                                <td class="py-2">{{ $student->academic_year }}</td>
                                <td class="py-2"><span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">{{ ucfirst($student->status) }}</span></td>
                                <td class="py-2"><a href="{{ route('staff.students.show', $student->id) }}" class="text-blue-600">View Details</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">{{ $students->links() }}</div>
            </div>
        </div>
    </div>
</x-staff-layout>