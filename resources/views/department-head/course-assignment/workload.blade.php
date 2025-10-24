<x-teacher-layout>
    <x-slot name="header">
        Teacher Workload Report - {{ $department->name }}
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b flex justify-between">
                    <h2 class="text-lg font-semibold">Faculty Workload</h2>
                    <a href="{{ route('department-head.course-assignment.index') }}" 
                       class="text-blue-600 hover:underline">
                        ← Back to Course Assignment
                    </a>
                </div>
                <div class="p-6">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-3">Teacher Name</th>
                                <th class="text-left py-3">Employee ID</th>
                                <th class="text-center py-3">Total Courses</th>
                                <th class="text-center py-3">Total Students</th>
                                <th class="text-center py-3">Total Credits</th>
                                <th class="text-left py-3">Specialization</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teachers as $teacher)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-3">
                                        <div class="flex items-center">
                                            @if($teacher->user->profile_image)
                                                <img class="h-10 w-10 rounded-full mr-3" src="{{ asset('storage/' . $teacher->user->profile_image) }}" alt="{{ $teacher->user->name }}">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold mr-3">
                                                    {{ strtoupper(substr($teacher->user->name, 0, 2)) }}
                                                </div>
                                            @endif
                                            <span class="font-medium">{{ $teacher->user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3">{{ $teacher->employee_id }}</td>
                                    <td class="text-center py-3">
                                        <span class="px-3 py-1 rounded-full text-sm font-semibold
                                            {{ $teacher->total_courses >= 4 ? 'bg-red-100 text-red-800' : ($teacher->total_courses >= 3 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                            {{ $teacher->total_courses }}
                                        </span>
                                    </td>
                                    <td class="text-center py-3">{{ $teacher->total_students }}</td>
                                    <td class="text-center py-3">{{ $teacher->total_credits }}</td>
                                    <td class="py-3 text-sm text-gray-600">{{ $teacher->specialization ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Summary -->
                    <div class="mt-6 grid grid-cols-3 gap-4">
                        <div class="bg-blue-50 p-4 rounded">
                            <p class="text-sm text-gray-600">Total Faculty</p>
                            <p class="text-2xl font-bold text-blue-600">{{ $teachers->count() }}</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded">
                            <p class="text-sm text-gray-600">Avg Courses/Teacher</p>
                            <p class="text-2xl font-bold text-green-600">
                                {{ $teachers->count() > 0 ? number_format($teachers->sum('total_courses') / $teachers->count(), 1) : 0 }}
                            </p>
                        </div>
                        <div class="bg-purple-50 p-4 rounded">
                            <p class="text-sm text-gray-600">Avg Students/Teacher</p>
                            <p class="text-2xl font-bold text-purple-600">
                                {{ $teachers->count() > 0 ? number_format($teachers->sum('total_students') / $teachers->count(), 0) : 0 }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-teacher-layout>

