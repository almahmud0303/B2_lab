<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Student Details') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">{{ $student->user->name }} - {{ $student->student_id }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.students.edit', $student) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Edit Student
                </a>
                <a href="{{ route('admin.students.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Student Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center space-x-6">
                        <div class="flex-shrink-0">
                            <div class="h-20 w-20 rounded-full bg-blue-100 flex items-center justify-center">
                                <span class="text-2xl font-medium text-blue-600">
                                    {{ substr($student->user->name, 0, 2) }}
                                </span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold text-gray-900">{{ $student->user->name }}</h3>
                            <p class="text-lg text-gray-600">{{ $student->student_id }}</p>
                            <p class="text-sm text-gray-500">{{ $student->department->name ?? 'No Department Assigned' }}</p>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $student->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $student->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Courses</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['total_courses'] }}</dd>
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
                                    <dt class="text-sm font-medium text-gray-500 truncate">Completed</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['completed_courses'] }}</dd>
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Results</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['total_results'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Average Grade</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['average_grade'], 1) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Personal Information -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h3>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $student->user->email }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $student->user->phone ?? 'Not provided' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Date of Birth</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $student->user->date_of_birth ? $student->user->date_of_birth->format('M d, Y') : 'Not provided' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Gender</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($student->user->gender ?? 'Not specified') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Current Semester</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $student->current_semester }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Admission Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $student->date_of_admission ? $student->date_of_admission->format('M d, Y') : 'Not provided' }}</dd>
                            </div>
                        </dl>
                        @if($student->user->address)
                            <div class="mt-6">
                                <dt class="text-sm font-medium text-gray-500">Address</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $student->user->address }}</dd>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Recent Enrollments -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Course Enrollments</h3>
                        <div class="space-y-3">
                            @forelse($recentEnrollments as $enrollment)
                                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $enrollment->course->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $enrollment->course->code }} | {{ $enrollment->course->credits }} credits</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-600">{{ $enrollment->enrollment_date->format('M d, Y') }}</p>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $enrollment->status === 'enrolled' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($enrollment->status) }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">No course enrollments found.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Recent Results -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Results</h3>
                        <div class="space-y-3">
                            @forelse($recentResults as $result)
                                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $result->exam->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $result->exam->course->name }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900">{{ $result->marks_obtained }}/{{ $result->exam->total_marks }}</p>
                                        <p class="text-xs text-gray-500">{{ $result->grade ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">No results found.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Fee Information -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Fee Information</h3>
                        <div class="space-y-3">
                            @forelse($fees as $fee)
                                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900">${{ number_format($fee->amount, 2) }}</p>
                                        <p class="text-sm text-gray-500">Due: {{ $fee->due_date->format('M d, Y') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $fee->status === 'paid' ? 'bg-green-100 text-green-800' : ($fee->status === 'unpaid' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ ucfirst($fee->status) }}
                                        </span>
                                        @if($fee->payment_date)
                                            <p class="text-xs text-gray-500 mt-1">Paid: {{ $fee->payment_date->format('M d, Y') }}</p>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">No fee records found.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
