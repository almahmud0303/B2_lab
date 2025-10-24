<x-teacher-layout>
    <x-slot name="header">
        Department Head Dashboard - {{ $department->name }}
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                    <p class="text-blue-100 text-sm">Total Teachers</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['total_teachers'] }}</p>
                </div>
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
                    <p class="text-green-100 text-sm">Total Students</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['total_students'] }}</p>
                </div>
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                    <p class="text-purple-100 text-sm">Total Courses</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['total_courses'] }}</p>
                </div>
                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
                    <p class="text-orange-100 text-sm">Active Courses</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['active_courses'] }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Department Teachers -->
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="px-6 py-4 border-b flex justify-between">
                        <h2 class="text-lg font-semibold">Department Faculty</h2>
                        <a href="{{ route('department-head.course-assignment.workload-report') }}" class="text-blue-600 text-sm">View Workload →</a>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach($teachers as $t)
                                <div class="border rounded p-3">
                                    <p class="font-semibold">{{ $t->user->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $t->employee_id }}</p>
                                    <p class="text-xs text-gray-500">{{ $t->specialization ?? 'N/A' }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Department Courses -->
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="px-6 py-4 border-b flex justify-between">
                        <h2 class="text-lg font-semibold">Department Courses</h2>
                        <a href="{{ route('department-head.course-assignment.index') }}" class="text-blue-600 text-sm">Manage Assignments →</a>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach($courses->take(10) as $course)
                                <div class="border rounded p-3">
                                    <p class="font-semibold">{{ $course->title }}</p>
                                    <p class="text-sm text-gray-600">{{ $course->course_code }} | Year {{ $course->academic_year }}, Sem {{ $course->semester }}</p>
                                    <p class="text-xs text-gray-500">Teacher: {{ $course->teacher->user->name ?? 'Unassigned' }}</p>
                                    <p class="text-xs text-gray-500">Enrolled: {{ $course->enrollments_count }} students</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-teacher-layout>

