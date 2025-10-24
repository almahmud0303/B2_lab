<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Fee Details: ' . $fee->fee_type) }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.fees.edit', $fee) }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Edit Fee
                </a>
                <a href="{{ route('admin.fees.index') }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                    Back to Fees
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Fee Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-2xl font-semibold text-gray-800">{{ $fee->fee_type }}</h3>
                            <p class="text-gray-600">Student: {{ $fee->student->user->name }}</p>
                            <p class="text-gray-600">Student ID: {{ $fee->student->student_id }}</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                @if($fee->status === 'paid') bg-green-100 text-green-800
                                @elseif($fee->status === 'partial') bg-yellow-100 text-yellow-800
                                @elseif($fee->status === 'overdue') bg-red-100 text-red-800
                                @else bg-blue-100 text-blue-800 @endif">
                                {{ ucfirst($fee->status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Fee Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-2">Fee Information</h4>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Fee Type</dt>
                                    <dd class="text-sm text-gray-900">{{ $fee->fee_type }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Total Amount</dt>
                                    <dd class="text-sm text-gray-900">${{ number_format($fee->amount, 2) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Paid Amount</dt>
                                    <dd class="text-sm text-gray-900">${{ number_format($fee->paid_amount, 2) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Remaining Amount</dt>
                                    <dd class="text-sm text-gray-900">${{ number_format($fee->amount - $fee->paid_amount, 2) }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-800 mb-2">Payment Information</h4>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Due Date</dt>
                                    <dd class="text-sm text-gray-900">{{ $fee->due_date->format('F d, Y') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Paid Date</dt>
                                    <dd class="text-sm text-gray-900">{{ $fee->paid_date ? $fee->paid_date->format('F d, Y') : 'Not Paid' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Created At</dt>
                                    <dd class="text-sm text-gray-900">{{ $fee->created_at->format('F d, Y H:i') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Updated At</dt>
                                    <dd class="text-sm text-gray-900">{{ $fee->updated_at->format('F d, Y H:i') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    @if($fee->notes)
                        <div class="mt-6">
                            <h4 class="font-semibold text-gray-800 mb-2">Notes</h4>
                            <p class="text-gray-700">{{ $fee->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Student Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Student Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Student Name</dt>
                                    <dd class="text-sm text-gray-900">{{ $fee->student->user->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="text-sm text-gray-900">{{ $fee->student->user->email }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                    <dd class="text-sm text-gray-900">{{ $fee->student->user->phone ?? 'Not Provided' }}</dd>
                                </div>
                            </dl>
                        </div>
                        <div>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Department</dt>
                                    <dd class="text-sm text-gray-900">{{ $fee->student->department->name ?? 'Not Assigned' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Academic Year</dt>
                                    <dd class="text-sm text-gray-900">{{ $fee->student->academic_year }} Year</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Semester</dt>
                                    <dd class="text-sm text-gray-900">{{ $fee->student->semester }} Semester</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Actions -->
            @if($fee->status !== 'paid')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Payment Actions</h3>
                        <div class="flex space-x-4">
                            <form method="POST" action="{{ route('admin.fees.mark-paid', $fee) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700"
                                        onclick="return confirm('Are you sure you want to mark this fee as paid?')">
                                    Mark as Paid
                                </button>
                            </form>
                            <a href="{{ route('admin.fees.edit', $fee) }}" 
                               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                                Edit Fee Details
                            </a>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
