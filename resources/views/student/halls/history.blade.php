<x-student-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hall Reservation History') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Reservation Statistics</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-blue-50 p-4 rounded-lg shadow-sm">
                            <p class="text-sm text-gray-600">Total Reservations</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_reservations'] }}</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg shadow-sm">
                            <p class="text-sm text-gray-600">Approved Reservations</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['approved_reservations'] }}</p>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg shadow-sm">
                            <p class="text-sm text-gray-600">Current Reservation</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['current_reservation'] ? 'Yes' : 'No' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reservation History -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">All Reservations</h3>
                    
                    @if($reservations->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hall</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reservation Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monthly Fee</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($reservations as $reservation)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $reservation->hall->name }}</div>
                                                <div class="text-sm text-gray-500">ID: {{ $reservation->id }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $reservation->hall->department->name ?? 'General' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $reservation->reservation_date->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $badgeClass = match($reservation->status) {
                                                        'active' => 'bg-green-100 text-green-800',
                                                        'approved' => 'bg-blue-100 text-blue-800',
                                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                                        'cancelled' => 'bg-red-100 text-red-800',
                                                        'rejected' => 'bg-red-100 text-red-800',
                                                        default => 'bg-gray-100 text-gray-800'
                                                    };
                                                @endphp
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $badgeClass }}">
                                                    {{ ucfirst($reservation->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                ${{ number_format($reservation->hall->monthly_fee, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('student.halls.reservation', $reservation) }}" 
                                                   class="text-blue-600 hover:text-blue-900">
                                                    View Details
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $reservations->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No reservations found</h3>
                            <p class="mt-1 text-sm text-gray-500">You haven't made any hall reservations yet.</p>
                            <div class="mt-6">
                                <a href="{{ route('student.halls.index') }}" 
                                   class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Browse Available Halls
                                </a>
                            </div>
                        </div>
                    @endif

                    <div class="mt-6 flex justify-end">
                        <a href="{{ route('student.halls.index') }}" 
                           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Back to Halls
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-student-layout>
