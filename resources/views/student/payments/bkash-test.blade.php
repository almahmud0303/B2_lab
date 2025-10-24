<x-student-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('bKash Payment Test') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <!-- bKash Payment Simulation -->
                    <div class="text-center mb-8">
                        <div class="bg-pink-100 p-4 rounded-lg mb-6">
                            <h3 class="text-lg font-semibold text-pink-800 mb-2">🧪 bKash Payment Test Mode</h3>
                            <p class="text-pink-700">This is a test simulation of the bKash payment process.</p>
                        </div>
                    </div>

                    <!-- Payment Details -->
                    <div class="bg-gray-50 p-6 rounded-lg mb-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Payment Details</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Payment ID</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $payment->payment_id }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Amount</label>
                                <p class="mt-1 text-sm text-gray-900">৳{{ number_format($payment->amount, 2) }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                                <p class="mt-1 text-sm text-gray-900">{{ ucfirst($payment->payment_method) }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <p class="mt-1 text-sm text-gray-900">{{ ucfirst($payment->status) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Simulated bKash Interface -->
                    <div class="bg-pink-50 border-2 border-pink-200 p-6 rounded-lg mb-6">
                        <h4 class="text-lg font-semibold text-pink-800 mb-4">📱 Simulated bKash Payment</h4>
                        
                        <div class="space-y-4">
                            <div class="bg-white p-4 rounded border">
                                <h5 class="font-semibold text-gray-800">Payment Information</h5>
                                <p class="text-gray-600">Amount: ৳{{ number_format($payment->amount, 2) }}</p>
                                <p class="text-gray-600">Reference: {{ $payment->payment_id }}</p>
                            </div>
                            
                            <div class="bg-white p-4 rounded border">
                                <h5 class="font-semibold text-gray-800">Instructions</h5>
                                <ol class="list-decimal list-inside text-gray-600 space-y-1">
                                    <li>Open your bKash app</li>
                                    <li>Go to "Send Money"</li>
                                    <li>Enter merchant number: 01770618575</li>
                                    <li>Enter amount: ৳{{ number_format($payment->amount, 2) }}</li>
                                    <li>Enter reference: {{ $payment->payment_id }}</li>
                                    <li>Complete the transaction</li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-center space-x-4">
                        <button onclick="simulateSuccess()" 
                                class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 font-semibold">
                            ✅ Simulate Success
                        </button>
                        <button onclick="simulateFailure()" 
                                class="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 font-semibold">
                            ❌ Simulate Failure
                        </button>
                        <a href="{{ route('student.fees.index') }}" 
                           class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 font-semibold">
                            Cancel
                        </a>
                    </div>

                    <!-- Hidden Form for Simulation -->
                    <form id="paymentForm" method="POST" action="{{ route('payment.callback') }}" style="display: none;">
                        @csrf
                        <input type="hidden" name="paymentID" id="paymentID" value="{{ $payment->payment_id }}">
                        <input type="hidden" name="status" id="status" value="">
                        <input type="hidden" name="transactionStatus" id="transactionStatus" value="">
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        function simulateSuccess() {
            document.getElementById('status').value = 'Completed';
            document.getElementById('transactionStatus').value = 'Completed';
            document.getElementById('paymentForm').submit();
        }

        function simulateFailure() {
            document.getElementById('status').value = 'Failed';
            document.getElementById('transactionStatus').value = 'Failed';
            document.getElementById('paymentForm').submit();
        }
    </script>
</x-student-layout>
