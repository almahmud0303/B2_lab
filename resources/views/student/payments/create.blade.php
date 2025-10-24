<x-student-layout>
    <x-slot name="header">Make Payment</x-slot>
    
    <div class="py-6">
        <div class="max-w-4xl mx-auto">
            <!-- Course Information -->
            <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">{{ $course->title }}</h2>
                        <p class="text-gray-600">{{ $course->course_code }} - {{ $course->credits }} Credits</p>
                        <p class="text-gray-600">{{ $course->department->name }}</p>
                        <p class="text-gray-600">Teacher: {{ $course->teacher->user->name }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold text-green-600">
                            {{ number_format($course->fee_amount, 2) }} {{ $course->currency }}
                        </div>
                        <p class="text-sm text-gray-500">Course Fee</p>
                    </div>
                </div>
            </div>

            <!-- Payment Form -->
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <h3 class="text-xl font-bold text-gray-800 mb-6">Payment Information</h3>
                
                <form method="POST" action="{{ route('student.payments.store', $course->id) }}">
                    @csrf
                    
                    <!-- Payment Method -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Payment Method *</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="relative">
                                <input type="radio" name="payment_method" value="bkash" class="sr-only" required>
                                <div class="border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-500 payment-method-option">
                                    <div class="flex items-center">
                                        <img src="https://via.placeholder.com/40x40/00A651/FFFFFF?text=bK" alt="bKash" class="w-10 h-10 rounded mr-3">
                                        <div>
                                            <h4 class="font-semibold">bKash</h4>
                                            <p class="text-sm text-gray-500">Mobile Banking</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            
                            <label class="relative">
                                <input type="radio" name="payment_method" value="nagad" class="sr-only" required>
                                <div class="border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-500 payment-method-option">
                                    <div class="flex items-center">
                                        <img src="https://via.placeholder.com/40x40/E31837/FFFFFF?text=NG" alt="Nagad" class="w-10 h-10 rounded mr-3">
                                        <div>
                                            <h4 class="font-semibold">Nagad</h4>
                                            <p class="text-sm text-gray-500">Mobile Banking</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            
                            <label class="relative">
                                <input type="radio" name="payment_method" value="rocket" class="sr-only" required>
                                <div class="border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-500 payment-method-option">
                                    <div class="flex items-center">
                                        <img src="https://via.placeholder.com/40x40/FF6B35/FFFFFF?text=RT" alt="Rocket" class="w-10 h-10 rounded mr-3">
                                        <div>
                                            <h4 class="font-semibold">Rocket</h4>
                                            <p class="text-sm text-gray-500">Mobile Banking</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            
                            <label class="relative">
                                <input type="radio" name="payment_method" value="bank_transfer" class="sr-only" required>
                                <div class="border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-500 payment-method-option">
                                    <div class="flex items-center">
                                        <img src="https://via.placeholder.com/40x40/1E40AF/FFFFFF?text=BT" alt="Bank Transfer" class="w-10 h-10 rounded mr-3">
                                        <div>
                                            <h4 class="font-semibold">Bank Transfer</h4>
                                            <p class="text-sm text-gray-500">Online Banking</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            
                            <label class="relative">
                                <input type="radio" name="payment_method" value="cash" class="sr-only" required>
                                <div class="border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-500 payment-method-option">
                                    <div class="flex items-center">
                                        <img src="https://via.placeholder.com/40x40/059669/FFFFFF?text=C$" alt="Cash" class="w-10 h-10 rounded mr-3">
                                        <div>
                                            <h4 class="font-semibold">Cash</h4>
                                            <p class="text-sm text-gray-500">Office Payment</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                        @error('payment_method')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone Number (for mobile banking) -->
                    <div class="mb-6" id="phone-number-field" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                        <input type="tel" name="phone_number" value="{{ old('phone_number') }}" 
                               class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               placeholder="01XXXXXXXXX">
                        @error('phone_number')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                        <textarea name="notes" rows="3" 
                                  class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                  placeholder="Any additional information...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Payment Summary -->
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <h4 class="font-semibold text-gray-800 mb-3">Payment Summary</h4>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600">Course Fee:</span>
                            <span class="font-semibold">{{ number_format($course->fee_amount, 2) }} {{ $course->currency }}</span>
                        </div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600">Processing Fee:</span>
                            <span class="font-semibold">0.00 {{ $course->currency }}</span>
                        </div>
                        <hr class="my-2">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-bold text-gray-800">Total Amount:</span>
                            <span class="text-lg font-bold text-green-600">{{ number_format($course->fee_amount, 2) }} {{ $course->currency }}</span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end gap-4">
                        <a href="{{ route('student.courses.index') }}" 
                           class="bg-gray-200 text-gray-800 px-6 py-3 rounded-lg hover:bg-gray-300 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                            Proceed to Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Show/hide phone number field based on payment method
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const phoneField = document.getElementById('phone-number-field');
                const mobileMethods = ['bkash', 'nagad', 'rocket'];
                
                if (mobileMethods.includes(this.value)) {
                    phoneField.style.display = 'block';
                    phoneField.querySelector('input').required = true;
                } else {
                    phoneField.style.display = 'none';
                    phoneField.querySelector('input').required = false;
                }
            });
        });

        // Visual feedback for payment method selection
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                // Remove active class from all options
                document.querySelectorAll('.payment-method-option').forEach(option => {
                    option.classList.remove('border-blue-500', 'bg-blue-50');
                    option.classList.add('border-gray-200');
                });
                
                // Add active class to selected option
                if (this.checked) {
                    const option = this.closest('label').querySelector('.payment-method-option');
                    option.classList.remove('border-gray-200');
                    option.classList.add('border-blue-500', 'bg-blue-50');
                }
            });
        });
    </script>
    @endpush
</x-student-layout>
