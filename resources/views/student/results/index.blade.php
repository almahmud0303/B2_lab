<x-student-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Results') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- CGPA Summary -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium mb-2">Cumulative Grade Point Average (CGPA)</h3>
                            <p class="text-sm opacity-90">Overall academic performance</p>
                        </div>
                        <div class="text-right">
                            <div class="text-5xl font-bold">{{ number_format($cgpa, 2) }}</div>
                            <div class="text-sm mt-1 opacity-90">out of 4.00</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Semester-wise GPA -->
            @if(count($semesterGPAs) > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Semester-wise GPA</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($semesterGPAs as $semester => $gpa)
                            <div class="border border-gray-200 rounded-lg p-4 text-center">
                                <div class="text-sm text-gray-600 mb-1">{{ $semester }}</div>
                                <div class="text-2xl font-bold text-blue-600">{{ number_format($gpa, 2) }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Results Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Published Results</h3>
                    
                    @if($results->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marks</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($results as $result)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $result->exam->course->course_code }}</div>
                                                <div class="text-sm text-gray-500">{{ $result->exam->course->title }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{ $result->exam->exam_type === 'midterm' ? 'bg-blue-100 text-blue-800' : '' }}
                                                    {{ $result->exam->exam_type === 'final' ? 'bg-purple-100 text-purple-800' : '' }}
                                                    {{ $result->exam->exam_type === 'quiz' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                                    {{ ucfirst($result->exam->exam_type) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $result->exam->exam_date ? $result->exam->exam_date->format('M d, Y') : 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <div class="font-medium">{{ $result->marks_obtained ?? 'N/A' }} / {{ $result->exam->total_marks ?? 100 }}</div>
                                                @if($result->marks_obtained && $result->exam->total_marks)
                                                    <div class="text-xs text-gray-500">
                                                        ({{ number_format(($result->marks_obtained / $result->exam->total_marks) * 100, 1) }}%)
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($result->grade)
                                                    <span class="px-3 py-1 inline-flex text-sm font-bold rounded-full
                                                        {{ in_array($result->grade, ['A+', 'A', 'A-']) ? 'bg-green-100 text-green-800' : '' }}
                                                        {{ in_array($result->grade, ['B+', 'B', 'B-']) ? 'bg-blue-100 text-blue-800' : '' }}
                                                        {{ in_array($result->grade, ['C+', 'C', 'C-']) ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                        {{ in_array($result->grade, ['D+', 'D', 'D-']) ? 'bg-orange-100 text-orange-800' : '' }}
                                                        {{ $result->grade === 'F' ? 'bg-red-100 text-red-800' : '' }}">
                                                        {{ $result->grade }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400">N/A</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('student.results.show', $result) }}" class="text-blue-600 hover:text-blue-900">View Details</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $results->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No Results Available</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Your exam results will appear here once they are published by your teachers.
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Grading Scale Reference -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Grading Scale</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
                        @php
                            $grades = [
                                'A+' => ['points' => '4.00', 'color' => 'green'],
                                'A' => ['points' => '3.75', 'color' => 'green'],
                                'A-' => ['points' => '3.50', 'color' => 'green'],
                                'B+' => ['points' => '3.25', 'color' => 'blue'],
                                'B' => ['points' => '3.00', 'color' => 'blue'],
                                'B-' => ['points' => '2.75', 'color' => 'blue'],
                                'C+' => ['points' => '2.50', 'color' => 'yellow'],
                                'C' => ['points' => '2.25', 'color' => 'yellow'],
                                'C-' => ['points' => '2.00', 'color' => 'yellow'],
                                'D+' => ['points' => '1.75', 'color' => 'orange'],
                                'D' => ['points' => '1.50', 'color' => 'orange'],
                                'D-' => ['points' => '1.25', 'color' => 'orange'],
                                'F' => ['points' => '0.00', 'color' => 'red'],
                            ];
                        @endphp
                        @foreach($grades as $grade => $info)
                            <div class="border border-{{ $info['color'] }}-200 bg-{{ $info['color'] }}-50 rounded-lg p-3 text-center">
                                <div class="text-lg font-bold text-{{ $info['color'] }}-800">{{ $grade }}</div>
                                <div class="text-xs text-{{ $info['color'] }}-600 mt-1">{{ $info['points'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-student-layout>

