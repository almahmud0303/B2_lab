<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Teacher Credentials - ' . $teacher->user->name) }}
            </h2>
            <div class="flex space-x-2">
                <button onclick="printCredentials()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    🖨️ Print Credentials
                </button>
                <a href="{{ route('admin.teachers.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Teachers
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Success Message -->
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <div class="flex">
                    <div class="py-1">
                        <svg class="fill-current h-6 w-6 text-green-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold">Teacher Created Successfully!</p>
                        <p class="text-sm">Please save or print these credentials for the teacher.</p>
                    </div>
                </div>
            </div>

            <!-- Credentials Card -->
            <div id="credentials-card" class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <!-- Header -->
                    <div class="text-center mb-8">
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">University Management System</h1>
                        <h2 class="text-xl text-gray-600">Teacher Login Credentials</h2>
                        <div class="mt-4 border-t border-gray-300"></div>
                    </div>

                    <!-- Teacher Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Teacher Information</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-600">Full Name:</span>
                                    <span class="text-gray-900">{{ $teacher->user->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-600">Employee ID:</span>
                                    <span class="text-gray-900 font-mono">{{ $teacher->employee_id }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-600">Department:</span>
                                    <span class="text-gray-900">{{ $teacher->department->name ?? 'Not Assigned' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-600">Designation:</span>
                                    <span class="text-gray-900">{{ $teacher->designation ?? 'Not Set' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-600">Joining Date:</span>
                                    <span class="text-gray-900">{{ $teacher->joining_date?->format('M d, Y') ?? 'Not Set' }}</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Login Credentials</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-600">Email:</span>
                                    <span class="text-gray-900 font-mono">{{ session('credentials.email') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-600">Password:</span>
                                    <span class="text-gray-900 font-mono bg-yellow-100 px-2 py-1 rounded">{{ session('credentials.password') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-600">Login URL:</span>
                                    <span class="text-blue-600 font-mono">{{ url('/login') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Important Instructions -->
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Important Instructions</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>Please provide these credentials to the teacher securely</li>
                                        <li>Teacher should change their password after first login</li>
                                        <li>Keep this information confidential and do not share publicly</li>
                                        <li>Teacher can access their dashboard at: <span class="font-mono">{{ url('/login') }}</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-center space-x-4">
                        <button onclick="printCredentials()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            Print Credentials
                        </button>
                        <button onclick="copyCredentials()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            Copy to Clipboard
                        </button>
                        <a href="{{ route('admin.teachers.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Teachers
                        </a>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center text-sm text-gray-500">
                <p>Generated on {{ now()->format('F d, Y \a\t g:i A') }}</p>
                <p>University Management System - Teacher Credentials</p>
            </div>
        </div>
    </div>

    <script>
        function printCredentials() {
            // Hide action buttons and header for printing
            const printElements = document.querySelectorAll('.no-print');
            printElements.forEach(el => el.style.display = 'none');
            
            // Print the credentials card
            const printContent = document.getElementById('credentials-card').innerHTML;
            const originalContent = document.body.innerHTML;
            
            document.body.innerHTML = `
                <div style="font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px;">
                    ${printContent}
                </div>
            `;
            
            window.print();
            
            // Restore original content
            document.body.innerHTML = originalContent;
            
            // Restore print elements
            printElements.forEach(el => el.style.display = '');
        }

        function copyCredentials() {
            const credentials = `
Teacher Login Credentials
========================

Teacher Information:
- Name: {{ $teacher->user->name }}
- Employee ID: {{ $teacher->employee_id }}
- Department: {{ $teacher->department->name ?? 'Not Assigned' }}
- Designation: {{ $teacher->designation ?? 'Not Set' }}

Login Credentials:
- Email: {{ session('credentials.email') }}
- Password: {{ session('credentials.password') }}
- Login URL: {{ url('/login') }}

Instructions:
- Teacher should change password after first login
- Keep credentials confidential
- Access dashboard at: {{ url('/login') }}

Generated on: {{ now()->format('F d, Y \a\t g:i A') }}
            `;
            
            navigator.clipboard.writeText(credentials).then(function() {
                alert('Credentials copied to clipboard!');
            }, function(err) {
                console.error('Could not copy text: ', err);
                alert('Failed to copy credentials. Please copy manually.');
            });
        }
    </script>
</x-app-layout>
