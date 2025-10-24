<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Fee: ' . $fee->fee_type) }}
            </h2>
            <a href="{{ route('admin.fees.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                Back to Fees
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-6">Edit Fee Information</h3>
                    
                    <form action="{{ route('admin.fees.update', $fee) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Student Selection -->
                            <div>
                                <label for="student_id" class="block text-sm font-medium text-gray-700">Student *</label>
                                <select name="student_id" id="student_id" 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('student_id') border-red-300 @enderror" 
                                        required>
                                    <option value="">Select a student</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}" 
                                                {{ old('student_id', $fee->student_id) == $student->id ? 'selected' : '' }}>
                                            {{ $student->user->name }} ({{ $student->student_id }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('student_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Fee Type -->
                            <div>
                                <label for="fee_type" class="block text-sm font-medium text-gray-700">Fee Type *</label>
                                <input type="text" name="fee_type" id="fee_type" 
                                       value="{{ old('fee_type', $fee->fee_type) }}"
                                       placeholder="e.g., Semester Fee, Library Fee, Lab Fee"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('fee_type') border-red-300 @enderror" 
                                       required>
                                @error('fee_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Amount -->
                            <div>
                                <label for="amount" class="block text-sm font-medium text-gray-700">Total Amount *</label>
                                <input type="number" name="amount" id="amount" 
                                       value="{{ old('amount', $fee->amount) }}"
                                       step="0.01" min="0"
                                       placeholder="0.00"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('amount') border-red-300 @enderror" 
                                       required>
                                @error('amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Paid Amount -->
                            <div>
                                <label for="paid_amount" class="block text-sm font-medium text-gray-700">Paid Amount</label>
                                <input type="number" name="paid_amount" id="paid_amount" 
                                       value="{{ old('paid_amount', $fee->paid_amount) }}"
                                       step="0.01" min="0"
                                       placeholder="0.00"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('paid_amount') border-red-300 @enderror">
                                @error('paid_amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Due Date -->
                            <div>
                                <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date *</label>
                                <input type="date" name="due_date" id="due_date" 
                                       value="{{ old('due_date', $fee->due_date->format('Y-m-d')) }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('due_date') border-red-300 @enderror" 
                                       required>
                                @error('due_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Paid Date -->
                            <div>
                                <label for="paid_date" class="block text-sm font-medium text-gray-700">Paid Date</label>
                                <input type="date" name="paid_date" id="paid_date" 
                                       value="{{ old('paid_date', $fee->paid_date ? $fee->paid_date->format('Y-m-d') : '') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('paid_date') border-red-300 @enderror">
                                @error('paid_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                                <select name="status" id="status" 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-300 @enderror" 
                                        required>
                                    <option value="pending" {{ old('status', $fee->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="paid" {{ old('status', $fee->status) == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="partial" {{ old('status', $fee->status) == 'partial' ? 'selected' : '' }}>Partial</option>
                                    <option value="overdue" {{ old('status', $fee->status) == 'overdue' ? 'selected' : '' }}>Overdue</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                            <textarea name="notes" id="notes" rows="4" 
                                      placeholder="Additional notes about this fee..."
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-300 @enderror">{{ old('notes', $fee->notes) }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('admin.fees.index') }}" 
                               class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                                Update Fee
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
