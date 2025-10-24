<x-student-layout>
    <x-slot name="header">Payment Instructions</x-slot>
    
    <div class="py-6">
        <div class="max-w-4xl mx-auto">
            <!-- Payment Status -->
            <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Payment Instructions</h2>
                        <p class="text-gray-600">Payment ID: {{ $payment->payment_id }}</p>
                        <p class="text-gray-600">Course: {{ $payment->course->title }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold text-blue-600">
                            {{ $payment->formatted_amount }}
                        </div>
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $payment->status_badge }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Payment Instructions -->
            <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
                @if($payment->payment_method === 'bkash')
                    <div class="text-center mb-6">
                        <img src="https://via.placeholder.com/80x80/00A651/FFFFFF?text=bK" alt="bKash" class="w-20 h-20 rounded mx-auto mb-4">
                        <h3 class="text-xl font-bold text-gray-800">bKash Payment Instructions</h3>
                    </div>
                    
                    <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
                        <h4 class="font-semibold text-green-800 mb-3">📱 How to Pay with bKash:</h4>
                        <ol class="list-decimal list-inside space-y-2 text-green-700">
                            <li>Open your bKash mobile app or dial *247#</li>
                            <li>Select "Send Money"</li>
                            <li>Enter the bKash merchant number: <strong>01XXXXXXXXX</strong></li>
                            <li>Enter amount: <strong>{{ $payment->formatted_amount }}</strong></li>
                            <li>Enter reference: <strong>{{ $payment->payment_id }}</strong></li>
                            <li>Enter your bKash PIN to confirm</li>
                            <li>Save the transaction ID for your records</li>
                        </ol>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                        <h4 class="font-semibold text-yellow-800 mb-2">⚠️ Important Notes:</h4>
                        <ul class="list-disc list-inside space-y-1 text-yellow-700">
                            <li>Make sure to enter the exact amount: {{ $payment->formatted_amount }}</li>
                            <li>Use this reference: {{ $payment->payment_id }}</li>
                            <li>Keep the transaction ID safe</li>
                            <li>Payment will be verified automatically</li>
                        </ul>
                    </div>

                @elseif($payment->payment_method === 'nagad')
                    <div class="text-center mb-6">
                        <img src="https://via.placeholder.com/80x80/E31837/FFFFFF?text=NG" alt="Nagad" class="w-20 h-20 rounded mx-auto mb-4">
                        <h3 class="text-xl font-bold text-gray-800">Nagad Payment Instructions</h3>
                    </div>
                    
                    <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-6">
                        <h4 class="font-semibold text-red-800 mb-3">📱 How to Pay with Nagad:</h4>
                        <ol class="list-decimal list-inside space-y-2 text-red-700">
                            <li>Open your Nagad mobile app or dial *167#</li>
                            <li>Select "Send Money"</li>
                            <li>Enter the Nagad merchant number: <strong>01XXXXXXXXX</strong></li>
                            <li>Enter amount: <strong>{{ $payment->formatted_amount }}</strong></li>
                            <li>Enter reference: <strong>{{ $payment->payment_id }}</strong></li>
                            <li>Enter your Nagad PIN to confirm</li>
                            <li>Save the transaction ID for your records</li>
                        </ol>
                    </div>

                @elseif($payment->payment_method === 'rocket')
                    <div class="text-center mb-6">
                        <img src="https://via.placeholder.com/80x80/FF6B35/FFFFFF?text=RT" alt="Rocket" class="w-20 h-20 rounded mx-auto mb-4">
                        <h3 class="text-xl font-bold text-gray-800">Rocket Payment Instructions</h3>
                    </div>
                    
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-6 mb-6">
                        <h4 class="font-semibold text-orange-800 mb-3">📱 How to Pay with Rocket:</h4>
                        <ol class="list-decimal list-inside space-y-2 text-orange-700">
                            <li>Open your Rocket mobile app or dial *322#</li>
                            <li>Select "Send Money"</li>
                            <li>Enter the Rocket merchant number: <strong>01XXXXXXXXX</strong></li>
                            <li>Enter amount: <strong>{{ $payment->formatted_amount }}</strong></li>
                            <li>Enter reference: <strong>{{ $payment->payment_id }}</strong></li>
                            <li>Enter your Rocket PIN to confirm</li>
                            <li>Save the transaction ID for your records</li>
                        </ol>
                    </div>

                @elseif($payment->payment_method === 'bank_transfer')
                    <div class="text-center mb-6">
                        <img src="https://via.placeholder.com/80x80/1E40AF/FFFFFF?text=BT" alt="Bank Transfer" class="w-20 h-20 rounded mx-auto mb-4">
                        <h3 class="text-xl font-bold text-gray-800">Bank Transfer Instructions</h3>
                    </div>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                        <h4 class="font-semibold text-blue-800 mb-3">🏦 Bank Transfer Details:</h4>
                        <div class="space-y-3 text-blue-700">
                            <div>
                                <strong>Bank Name:</strong> University Bank<br>
                                <strong>Account Name:</strong> University Course Fees<br>
                                <strong>Account Number:</strong> 1234567890123456<br>
                                <strong>Amount:</strong> {{ $payment->formatted_amount }}<br>
                                <strong>Reference:</strong> {{ $payment->payment_id }}
                            </div>
                        </div>
                    </div>

                @elseif($payment->payment_method === 'cash')
                    <div class="text-center mb-6">
                        <img src="https://via.placeholder.com/80x80/059669/FFFFFF?text=C$" alt="Cash" class="w-20 h-20 rounded mx-auto mb-4">
                        <h3 class="text-xl font-bold text-gray-800">Cash Payment Instructions</h3>
                    </div>
                    
                    <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
                        <h4 class="font-semibold text-green-800 mb-3">💰 Cash Payment Details:</h4>
                        <div class="space-y-3 text-green-700">
                            <div>
                                <strong>Amount to Pay:</strong> {{ $payment->formatted_amount }}<br>
                                <strong>Payment Reference:</strong> {{ $payment->payment_id }}<br>
                                <strong>Office Location:</strong> University Administration Building<br>
                                <strong>Office Hours:</strong> 9:00 AM - 5:00 PM (Sunday to Thursday)<br>
                                <strong>Contact:</strong> +880-XXX-XXXXXXX
                            </div>
                        </div>
                    </div>
                @endif

                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                    <h4 class="font-semibold text-gray-800 mb-3">📋 What to do next:</h4>
                    <ol class="list-decimal list-inside space-y-2 text-gray-700">
                        <li>Complete the payment using the method above</li>
                        <li>Keep your transaction details safe</li>
                        <li>Payment will be verified within 24-48 hours</li>
                        <li>You'll receive a confirmation email once verified</li>
                        <li>Check your payment status in the payment history</li>
                    </ol>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Need Help?</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="font-semibold text-gray-700 mb-2">Payment Support</h4>
                        <p class="text-gray-600">Email: payments@university.edu</p>
                        <p class="text-gray-600">Phone: +880-XXX-XXXXXXX</p>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-700 mb-2">Office Hours</h4>
                        <p class="text-gray-600">Sunday to Thursday: 9:00 AM - 5:00 PM</p>
                        <p class="text-gray-600">Friday: Closed</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="bg-white p-6 rounded-lg shadow-sm mt-6">
                <div class="flex justify-between items-center">
                    <a href="{{ route('student.payments.show', $payment->id) }}" 
                       class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300">
                        Back to Payment Details
                    </a>
                    
                    <a href="{{ route('student.payments.history') }}" 
                       class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                        View Payment History
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-student-layout>
