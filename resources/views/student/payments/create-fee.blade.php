<x-student-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pay Fee') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Fee Payment Details</h3>
                        
                        <!-- Fee Information -->
                        <div class="bg-gray-50 p-4 rounded-lg mb-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Fee Type</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $fee->fee_type }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Total Amount</label>
                                    <p class="mt-1 text-sm text-gray-900">৳{{ number_format($fee->amount, 2) }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Paid Amount</label>
                                    <p class="mt-1 text-sm text-gray-900">৳{{ number_format($fee->paid_amount, 2) }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Remaining Amount</label>
                                    <p class="mt-1 text-sm font-bold text-red-600">৳{{ number_format($fee->amount - $fee->paid_amount, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Form -->
                    <form action="{{ route('student.payments.store', $courseId) }}" method="POST" class="space-y-6">
                        @csrf
                        <input type="hidden" name="fee_id" value="{{ $fee->id }}">
                        <input type="hidden" name="amount" value="{{ $formData['amount'] }}">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Payment Amount -->
                            <div>
                                <label for="amount_display" class="block text-sm font-medium text-gray-700">Payment Amount</label>
                                <div class="mt-1">
                                    <input type="text" id="amount_display" 
                                           value="৳{{ number_format($formData['amount'], 2) }}" 
                                           class="w-full border rounded px-4 py-2 bg-gray-50" 
                                           readonly>
                                </div>
                            </div>

                            <!-- Payment Method -->
                            <div>
                                <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method</label>
                                <select name="payment_method" id="payment_method" 
                                        class="w-full border rounded px-4 py-2" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="bkash" {{ ($formData['payment_method'] == 'bkash' || $formData['payment_method'] == 'mobile_banking') ? 'selected' : '' }}>bKash</option>
                                    <option value="nagad" {{ $formData['payment_method'] == 'nagad' ? 'selected' : '' }}>Nagad</option>
                                    <option value="rocket" {{ $formData['payment_method'] == 'rocket' ? 'selected' : '' }}>Rocket</option>
                                    <option value="bank_transfer" {{ $formData['payment_method'] == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="cash" {{ $formData['payment_method'] == 'cash' ? 'selected' : '' }}>Cash</option>
                                </select>
                                @error('payment_method')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Phone Number (for mobile banking) -->
                        <div id="phone_number_field" style="display: none;">
                            <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <div class="mt-1">
                                <input type="text" name="phone_number" id="phone_number" 
                                       value="{{ $formData['phone_number'] }}"
                                       placeholder="01XXXXXXXXX" 
                                       class="w-full border rounded px-4 py-2">
                                <p class="mt-1 text-xs text-gray-500">Enter your mobile number for mobile banking</p>
                            </div>
                            @error('phone_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
                            <textarea name="notes" id="notes" rows="3" 
                                      placeholder="Any additional notes about this payment..."
                                      class="w-full border rounded px-4 py-2">{{ $formData['notes'] }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="terms_accepted" id="terms_accepted" 
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" required>
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="terms_accepted" class="font-medium text-gray-700">
                                    I agree to the terms and conditions
                                </label>
                                <p class="text-gray-500">By proceeding, you agree to our payment terms and conditions.</p>
                            </div>
                        </div>
                        @error('terms_accepted')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <!-- Submit Button -->
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('student.fees.index') }}" 
                               class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                                Process Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('payment_method').addEventListener('change', function() {
            const phoneField = document.getElementById('phone_number_field');
            const phoneInput = document.getElementById('phone_number');
            
            if (['bkash', 'nagad', 'rocket'].includes(this.value)) {
                phoneField.style.display = 'block';
                phoneInput.required = true;
            } else {
                phoneField.style.display = 'none';
                phoneInput.required = false;
            }
        });

        // Trigger change event on page load
        document.getElementById('payment_method').dispatchEvent(new Event('change'));
    </script>
</x-student-layout>
