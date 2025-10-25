<x-student-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Courses') }}
            </h2>
            <a href="{{ route('student.courses.catalog') }}" class="bg-green-500 hover:bg-green-700 text-white text-sm py-1 px-3 rounded">
                Browse Optional Courses
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Course Summary -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-sm text-gray-500 mb-2">Compulsory Courses</h3>
                        <p class="text-3xl font-bold text-red-600">{{ $compulsoryCourses->count() }}</p>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-sm text-gray-500 mb-2">Optional Courses</h3>
                        <p class="text-3xl font-bold text-green-600">{{ $optionalCourses->count() }}</p>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-sm text-gray-500 mb-2">Total Credits</h3>
                        <p class="text-3xl font-bold text-blue-600">{{ $totalCredits }}</p>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-sm text-gray-500 mb-2">Completed</h3>
                        <p class="text-3xl font-bold text-purple-600">{{ $completedCourses->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Compulsory Courses -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Compulsory Courses ({{ $compulsoryCourses->count() }})
                        <span class="text-sm font-normal text-gray-500">- Required for graduation</span>
                    </h3>

                    @if($compulsoryCourses->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course Title</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teacher</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Credits</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fee</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Enrollment Date</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($compulsoryCourses as $enrollment)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $enrollment->course->course_code }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-900">
                                                {{ $enrollment->course->title }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600">
                                                {{ $enrollment->course->teacher->user->name }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                                {{ $enrollment->course->credits }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                                {{ number_format($enrollment->course->fee_amount, 2) }} {{ $enrollment->course->currency }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                                {{ $enrollment->enrollment_date->format('M d, Y') }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Compulsory
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                                @if($enrollment->course->fee_required && $enrollment->course->fee_amount > 0)
                                                    <a href="{{ route('student.payments.create', $enrollment->course->id) }}" 
                                                       class="bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700">
                                                        Pay Fee
                                                    </a>
                                                @else
                                                    <span class="text-gray-400 text-xs">No Fee</span>
                                                @endif
                                            </td>
                                        </tr>
                            @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500">No compulsory courses enrolled yet.</p>
                            <p class="text-sm text-gray-400 mt-2">Compulsory courses will be auto-enrolled based on your year and semester.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Optional Courses -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            Optional Courses ({{ $optionalCourses->count() }})
                            <span class="text-sm font-normal text-gray-500">- Electives you chose</span>
                        </h3>
                        <a href="{{ route('student.courses.catalog') }}" class="text-green-600 hover:text-green-900 text-sm font-medium">
                            + Browse more optional courses
                        </a>
                    </div>

                    @if($optionalCourses->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course Title</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teacher</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Credits</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fee</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Enrollment Date</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($optionalCourses as $enrollment)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $enrollment->course->course_code }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-900">
                                                {{ $enrollment->course->title }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600">
                                                {{ $enrollment->course->teacher->user->name }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                                {{ $enrollment->course->credits }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                                {{ number_format($enrollment->course->fee_amount, 2) }} {{ $enrollment->course->currency }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                                {{ $enrollment->enrollment_date->format('M d, Y') }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                                <div class="flex gap-2">
                                                    @if($enrollment->course->fee_required && $enrollment->course->fee_amount > 0)
                                                        <a href="{{ route('student.payments.create', $enrollment->course->id) }}" 
                                                           class="bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700">
                                                            Pay Fee
                                                        </a>
                                                    @endif
                                                    <form method="POST" action="{{ route('student.courses.drop', $enrollment->course) }}" class="inline"
                                                          onsubmit="return confirm('Are you sure you want to drop this course?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900 font-medium text-xs">
                                                            Drop
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500">You haven't enrolled in any optional courses yet.</p>
                            <div class="mt-4">
                                <a href="{{ route('student.courses.catalog') }}" class="inline-flex items-center px-4 py-2 bg-green-500 hover:bg-green-700 text-white font-bold rounded">
                                    Browse Optional Courses
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Completed Courses -->
            @if($completedCourses->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Completed Courses ({{ $completedCourses->count() }})
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course Title</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Credits</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grade</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($completedCourses as $enrollment)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $enrollment->course->course_code }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            {{ $enrollment->course->title }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                            {{ $enrollment->course->credits }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-blue-600">
                                            {{ $enrollment->grade ?? 'Pending' }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $enrollment->course->course_type === 'compulsory' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                                {{ ucfirst($enrollment->course->course_type) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-student-layout>
