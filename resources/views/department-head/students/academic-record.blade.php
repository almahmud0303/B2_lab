<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Academic Record') }} - {{ $student->user->name }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $department->name ?? 'No Department Assigned' }}
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('department-head.students.show', $student) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Student Profile
                </a>
                <a href="{{ route('department-head.students.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                    Back to Students
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Student Summary -->
            <div class="bg-white rounded-lg shadow-sm mb-6">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold">Student Summary</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Student ID</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $student->student_id }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Academic Year</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $student->academic_year }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Semester</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $student->semester }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $student->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $student->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic Records by Year and Semester -->
            @if($academicRecords->count() > 0)
                @foreach($academicRecords as $yearSemester => $enrollments)
                    <div class="bg-white rounded-lg shadow-sm mb-6">
                        <div class="px-6 py-4 border-b bg-gray-50">
                            <h3 class="text-lg font-semibold">{{ $yearSemester }}</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teacher</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Credits</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">GPA</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($enrollments as $enrollment)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $enrollment->course->title }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $enrollment->course->course_code }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $enrollment->course->teacher->user->name ?? 'Not Assigned' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $enrollment->course->credits }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                    {{ $enrollment->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                                       ($enrollment->status === 'enrolled' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                                    {{ ucfirst($enrollment->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @if($enrollment->results->count() > 0)
                                                    @php
                                                        $latestResult = $enrollment->results->sortByDesc('created_at')->first();
                                                    @endphp
                                                    <div>
                                                        <span class="font-medium">{{ $latestResult->grade }}</span>
                                                        <div class="text-xs text-gray-500">{{ $latestResult->marks_obtained }}/{{ $latestResult->total_marks }}</div>
                                                    </div>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @if($enrollment->results->count() > 0)
                                                    @php
                                                        $latestResult = $enrollment->results->sortByDesc('created_at')->first();
                                                    @endphp
                                                    {{ number_format($latestResult->grade_points, 2) }}
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Semester Summary -->
                        <div class="px-6 py-4 border-t bg-gray-50">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <span class="font-medium text-gray-700">Total Credits:</span>
                                    <span class="ml-2">{{ $enrollments->sum('course.credits') }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Completed Credits:</span>
                                    <span class="ml-2">{{ $enrollments->where('status', 'completed')->sum('course.credits') }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Semester GPA:</span>
                                    <span class="ml-2">
                                        @php
                                            $completedCourses = $enrollments->where('status', 'completed');
                                            $totalPoints = 0;
                                            $totalCredits = 0;
                                            foreach($completedCourses as $enrollment) {
                                                if($enrollment->results->count() > 0) {
                                                    $latestResult = $enrollment->results->sortByDesc('created_at')->first();
                                                    $totalPoints += $latestResult->grade_points * $enrollment->course->credits;
                                                    $totalCredits += $enrollment->course->credits;
                                                }
                                            }
                                            $semesterGPA = $totalCredits > 0 ? $totalPoints / $totalCredits : 0;
                                        @endphp
                                        {{ number_format($semesterGPA, 2) }}
                                    </span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Courses:</span>
                                    <span class="ml-2">{{ $enrollments->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="bg-white p-12 rounded-lg shadow-sm text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">No Academic Records</h3>
                    <p class="text-gray-500">This student has no course enrollments yet.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
