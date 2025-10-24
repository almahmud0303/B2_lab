<x-student-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reports & Analytics') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Overview Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Academic Performance -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">Academic Performance</h3>
                                <p class="text-sm text-gray-600">Current CGPA: {{ number_format($academicStats['cgpa'], 2) }}</p>
                                <p class="text-sm text-gray-600">Completed Courses: {{ $academicStats['completed_courses'] }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('student.reports.academic') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View Academic Report →
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Fee Status -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">Fee Status</h3>
                                <p class="text-sm text-gray-600">Total Paid: ${{ number_format($feeStats['total_paid'], 2) }}</p>
                                <p class="text-sm text-gray-600">Pending: ${{ number_format($feeStats['pending_amount'], 2) }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('student.reports.fee') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View Fee Report →
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Library Activity -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">Library Activity</h3>
                                <p class="text-sm text-gray-600">Books Borrowed: {{ $libraryStats['total_borrowed'] }}</p>
                                <p class="text-sm text-gray-600">Currently Borrowed: {{ $libraryStats['currently_borrowed'] }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('student.reports.library') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View Library Report →
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Available Reports -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Available Reports</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Academic Report -->
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center mb-3">
                                <svg class="h-8 w-8 text-blue-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h4 class="text-lg font-semibold text-gray-900">Academic Report</h4>
                            </div>
                            <p class="text-gray-600 text-sm mb-4">View your academic performance, grades, and transcript.</p>
                            <div class="flex space-x-2">
                                <a href="{{ route('student.reports.academic') }}" 
                                   class="flex-1 bg-blue-500 hover:bg-blue-700 text-white text-center py-2 px-3 rounded text-sm font-medium">
                                    View
                                </a>
                                <a href="{{ route('student.reports.academic-download') }}" 
                                   class="flex-1 bg-gray-500 hover:bg-gray-700 text-white text-center py-2 px-3 rounded text-sm font-medium">
                                    Download
                                </a>
                            </div>
                        </div>

                        <!-- Fee Report -->
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center mb-3">
                                <svg class="h-8 w-8 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
                                </svg>
                                <h4 class="text-lg font-semibold text-gray-900">Fee Report</h4>
                            </div>
                            <p class="text-gray-600 text-sm mb-4">View your fee payment history and outstanding amounts.</p>
                            <div class="flex space-x-2">
                                <a href="{{ route('student.reports.fee') }}" 
                                   class="flex-1 bg-blue-500 hover:bg-blue-700 text-white text-center py-2 px-3 rounded text-sm font-medium">
                                    View
                                </a>
                                <a href="{{ route('student.reports.fee-download') }}" 
                                   class="flex-1 bg-gray-500 hover:bg-gray-700 text-white text-center py-2 px-3 rounded text-sm font-medium">
                                    Download
                                </a>
                            </div>
                        </div>

                        <!-- Library Report -->
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center mb-3">
                                <svg class="h-8 w-8 text-purple-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"></path>
                                </svg>
                                <h4 class="text-lg font-semibold text-gray-900">Library Report</h4>
                            </div>
                            <p class="text-gray-600 text-sm mb-4">View your library borrowing history and current books.</p>
                            <div class="flex space-x-2">
                                <a href="{{ route('student.reports.library') }}" 
                                   class="flex-1 bg-blue-500 hover:bg-blue-700 text-white text-center py-2 px-3 rounded text-sm font-medium">
                                    View
                                </a>
                                <a href="{{ route('student.reports.library-download') }}" 
                                   class="flex-1 bg-gray-500 hover:bg-gray-700 text-white text-center py-2 px-3 rounded text-sm font-medium">
                                    Download
                                </a>
                            </div>
                        </div>

                        <!-- Transcript -->
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center mb-3">
                                <svg class="h-8 w-8 text-yellow-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                </svg>
                                <h4 class="text-lg font-semibold text-gray-900">Official Transcript</h4>
                            </div>
                            <p class="text-gray-600 text-sm mb-4">Download your official academic transcript.</p>
                            <div class="flex space-x-2">
                                <a href="{{ route('student.reports.transcript') }}" 
                                   class="flex-1 bg-blue-500 hover:bg-blue-700 text-white text-center py-2 px-3 rounded text-sm font-medium">
                                    View
                                </a>
                                <a href="{{ route('student.reports.transcript-download') }}" 
                                   class="flex-1 bg-gray-500 hover:bg-gray-700 text-white text-center py-2 px-3 rounded text-sm font-medium">
                                    Download
                                </a>
                            </div>
                        </div>

                        <!-- Comprehensive Report -->
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center mb-3">
                                <svg class="h-8 w-8 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <h4 class="text-lg font-semibold text-gray-900">Comprehensive Report</h4>
                            </div>
                            <p class="text-gray-600 text-sm mb-4">Complete overview of all your academic and financial records.</p>
                            <div class="flex space-x-2">
                                <a href="{{ route('student.reports.comprehensive') }}" 
                                   class="flex-1 bg-blue-500 hover:bg-blue-700 text-white text-center py-2 px-3 rounded text-sm font-medium">
                                    View
                                </a>
                                <a href="{{ route('student.reports.comprehensive-download') }}" 
                                   class="flex-1 bg-gray-500 hover:bg-gray-700 text-white text-center py-2 px-3 rounded text-sm font-medium">
                                    Download
                                </a>
                            </div>
                        </div>

                        <!-- Analytics Dashboard -->
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center mb-3">
                                <svg class="h-8 w-8 text-indigo-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                                </svg>
                                <h4 class="text-lg font-semibold text-gray-900">Analytics Dashboard</h4>
                            </div>
                            <p class="text-gray-600 text-sm mb-4">Visual analytics and performance insights.</p>
                            <div class="flex space-x-2">
                                <a href="{{ route('student.reports.analytics') }}" 
                                   class="flex-1 bg-blue-500 hover:bg-blue-700 text-white text-center py-2 px-3 rounded text-sm font-medium">
                                    View Analytics
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Activity</h3>
                    
                    <div class="space-y-4">
                        <!-- Sample recent activities -->
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Fee payment completed</p>
                                <p class="text-xs text-gray-600">2 days ago</p>
                            </div>
                        </div>

                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">New exam result published</p>
                                <p class="text-xs text-gray-600">1 week ago</p>
                            </div>
                        </div>

                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Library book returned</p>
                                <p class="text-xs text-gray-600">2 weeks ago</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-student-layout>
