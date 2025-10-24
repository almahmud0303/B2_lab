<x-student-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Make Payment') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <!-- Fee Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Fee Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Fee Type</dt>
                                    <dd class="text-sm text-gray-900">{{ ucfirst($fee->fee_type) }} Fee</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Due Date</dt>
                                    <dd class="text-sm text-gray-900">{{ $fee->due_date->format('M d, Y') }}</dd>
                                </div>
                            </dl>
                        </div>
                        <div>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Total Amount</dt>
                                    <dd class="text-lg font-bold text-gray-900">${{ number_format($fee->amount, 2) }}</dd>
                                </div>
                                @if($fee->paid_amount > 0)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Already Paid</dt>
                                    <dd class="text-sm text-gray-900">${{ number_format($fee->paid_amount, 2) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Remaining Amount</dt>
                                    <dd class="text-lg font-bold text-red-600">${{ number_format($fee->amount - $fee->paid_amount, 2) }}</dd>
                                </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Details</h3>
                    
                    <form method="POST" action="{{ route('student.fees.process-payment', $fee) }}">
                        @csrf
                        
                        <!-- Amount to Pay -->
                        <div class="mb-6">
                            <label for="amount" class="block text-sm font-medium text-gray-700">Amount to Pay</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input type="number" 
                                       name="amount" 
                                       id="amount" 
                                       step="0.01"
                                       min="0.01"
                                       max="{{ $fee->amount - $fee->paid_amount }}"
                                       value="{{ $fee->amount - $fee->paid_amount }}"
                                       class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md"
                                       required>
                            </div>
                            @error('amount')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Payment Method -->
                        <div class="mb-6">
                            <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method</label>
                            <select name="payment_method" 
                                    id="payment_method" 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    required>
                                <option value="">Select Payment Method</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="debit_card">Debit Card</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="mobile_banking">Mobile Banking</option>
                            </select>
                            @error('payment_method')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Transaction Reference -->
                        <div class="mb-6">
                            <label for="transaction_reference" class="block text-sm font-medium text-gray-700">Transaction Reference</label>
                            <input type="text" 
                                   name="transaction_reference" 
                                   id="transaction_reference"
                                   placeholder="Enter transaction reference or receipt number"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('transaction_reference')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-sm text-gray-500">Enter the transaction reference number from your payment confirmation.</p>
                        </div>

                        <!-- Payment Notes -->
                        <div class="mb-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Additional Notes</label>
                            <textarea name="notes" 
                                      id="notes" 
                                      rows="3"
                                      placeholder="Any additional information about this payment"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                            @error('notes')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="mb-6">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" 
                                           name="terms_accepted" 
                                           id="terms_accepted"
                                           class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                                           required>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="terms_accepted" class="text-gray-700">
                                        I agree to the <a href="#" class="text-blue-600 hover:text-blue-500">payment terms and conditions</a>
                                    </label>
                                </div>
                            </div>
                            @error('terms_accepted')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                            <a href="{{ route('student.fees.show', $fee) }}" 
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Process Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Payment Instructions -->
            <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Payment Instructions</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Make sure you have the exact amount ready for payment</li>
                                <li>Keep your payment confirmation/receipt for reference</li>
                                <li>Payment will be verified within 1-2 business days</li>
                                <li>Contact finance office if you have any questions</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-student-layout>
