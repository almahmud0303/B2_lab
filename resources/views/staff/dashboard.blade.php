<x-staff-layout>
    <x-slot name="header">
        Staff Dashboard
    </x-slot>

    <!-- Welcome Section -->
    <div class="mb-6 bg-white rounded-lg shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">
            Welcome, {{ Auth::user()->name }}!
        </h1>
        <p class="text-gray-600">
            {{ Auth::user()->staff->position ?? 'Staff Member' }} | Employee ID: {{ Auth::user()->staff->employee_id ?? 'N/A' }}
        </p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Total Books -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Books</p>
                    <p class="text-3xl font-bold mt-2">{{ $libraryStats['total_books'] }}</p>
                </div>
                <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Issued Books -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Issued Books</p>
                    <p class="text-3xl font-bold mt-2">{{ $libraryStats['issued_books'] }}</p>
                </div>
                <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Overdue Books -->
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm font-medium">Overdue Books</p>
                    <p class="text-3xl font-bold mt-2">{{ $libraryStats['overdue_books'] }}</p>
                </div>
                <div class="bg-red-400 bg-opacity-30 rounded-full p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Available Halls -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Available Halls</p>
                    <p class="text-3xl font-bold mt-2">{{ $hallStats['available_halls'] }}</p>
                </div>
                <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Recent Book Issues -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800">Recent Book Issues</h2>
                    <a href="{{ route('staff.book-issues.index') }}" class="text-green-600 hover:text-green-800 text-sm font-medium">
                        View All →
                    </a>
                </div>
                <div class="p-6">
                    @if($recentBookIssues->isEmpty())
                        <p class="text-gray-500 text-center py-8">No recent book issues.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($recentBookIssues as $issue)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                @if($issue->student && $issue->student->user)
                                                    <div class="text-sm font-medium text-gray-900">{{ $issue->student->user->name }}</div>
                                                    <div class="text-xs text-gray-500">{{ $issue->student->student_id }}</div>
                                                @else
                                                    <div class="text-sm font-medium text-red-500">Student Not Found</div>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="text-sm text-gray-900">{{ Str::limit($issue->book->title, 30) }}</div>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                @if($issue->status === 'issued')
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Issued</span>
                                                @elseif($issue->status === 'returned')
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Returned</span>
                                                @elseif($issue->status === 'overdue')
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Overdue</span>
                                                @else
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ ucfirst($issue->status) }}</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                {{ $issue->created_at->format('d M, Y') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Overdue Books -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800">Overdue Books</h2>
                    <a href="{{ route('staff.book-issues.index', ['status' => 'overdue']) }}" class="text-red-600 hover:text-red-800 text-sm font-medium">
                        View All →
                    </a>
                </div>
                <div class="p-6">
                    @if($overdueBooks->isEmpty())
                        <p class="text-gray-500 text-center py-8">No overdue books.</p>
                    @else
                        <div class="space-y-3">
                            @foreach($overdueBooks as $issue)
                                <div class="flex items-center justify-between border-l-4 border-red-500 bg-red-50 p-4 rounded-r-lg">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-800 text-sm">
                                            @if($issue->student && $issue->student->user)
                                                {{ $issue->student->user->name }}
                                            @else
                                                <span class="text-red-500">Student Not Found</span>
                                            @endif
                                        </h3>
                                        <p class="text-sm text-gray-600 mt-1">{{ Str::limit($issue->book->title, 40) }}</p>
                                        <p class="text-xs text-red-600 mt-1">
                                            Due: {{ $issue->due_date->format('d M, Y') }}
                                        </p>
                                    </div>
                                    <a href="{{ route('staff.book-issues.show', $issue->id) }}" 
                                       class="ml-4 bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 text-xs">
                                        View
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Recent Notices -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800">Recent Notices</h2>
                    <a href="{{ route('staff.notices.index') }}" class="text-green-600 hover:text-green-800 text-sm font-medium">
                        View All →
                    </a>
                </div>
                <div class="p-6">
                    @if($recentNotices->isEmpty())
                        <p class="text-gray-500 text-center py-8">No notices available.</p>
                    @else
                        <div class="space-y-3">
                            @foreach($recentNotices as $notice)
                                <div class="border-b border-gray-100 pb-3 last:border-0">
                                    <h3 class="font-medium text-gray-800 text-sm">{{ Str::limit($notice->title, 50) }}</h3>
                                    <p class="text-xs text-gray-500 mt-1">{{ $notice->created_at->diffForHumans() }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Quick Actions</h2>
                </div>
                <div class="p-6 space-y-2">
                    <a href="{{ route('staff.book-issues.create') }}" 
                       class="flex items-center p-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                        <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-700">Issue New Book</span>
                    </a>
                    
                    <a href="{{ route('staff.library.create') }}" 
                       class="flex items-center p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                        <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-700">Add New Book</span>
                    </a>
                    
                    <a href="{{ route('staff.students.index') }}" 
                       class="flex items-center p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                        <svg class="w-5 h-5 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-700">View Students</span>
                    </a>
                    
                    <a href="{{ route('staff.profile.index') }}" 
                       class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-700">View Profile</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-staff-layout>
