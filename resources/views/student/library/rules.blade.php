<x-student-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Library Rules & Regulations') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Library Rules -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Borrowing Rules</h3>
                    <div class="space-y-4">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="font-medium text-blue-900 mb-2">📚 Borrowing Limits</h4>
                            <ul class="text-sm text-blue-800 space-y-1">
                                <li>• Maximum 5 books per student at any time</li>
                                <li>• Books can be borrowed for 14 days</li>
                                <li>• Books can be renewed up to 2 times (if not requested by others)</li>
                                <li>• Renewal must be done before the due date</li>
                            </ul>
                        </div>

                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <h4 class="font-medium text-green-900 mb-2">✅ Eligibility</h4>
                            <ul class="text-sm text-green-800 space-y-1">
                                <li>• All enrolled students are eligible to borrow books</li>
                                <li>• Students must present their student ID card</li>
                                <li>• Students with overdue books cannot borrow new books</li>
                                <li>• Students with outstanding fines must clear them first</li>
                            </ul>
                        </div>

                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <h4 class="font-medium text-yellow-900 mb-2">⚠️ Fines & Penalties</h4>
                            <ul class="text-sm text-yellow-800 space-y-1">
                                <li>• Fine rate: $1.00 per day for overdue books</li>
                                <li>• Fines accumulate daily until the book is returned</li>
                                <li>• Lost or damaged books must be replaced or paid for</li>
                                <li>• Students with outstanding fines may have borrowing privileges suspended</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Library Conduct -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Library Conduct</h3>
                    <div class="space-y-4">
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">📖 General Rules</h4>
                            <ul class="text-sm text-gray-700 space-y-1">
                                <li>• Maintain silence and respect other users</li>
                                <li>• No food or drinks allowed in the library</li>
                                <li>• Mobile phones should be on silent mode</li>
                                <li>• Do not reshelf books - leave them on the designated tables</li>
                                <li>• Report any damaged books to library staff</li>
                            </ul>
                        </div>

                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                            <h4 class="font-medium text-purple-900 mb-2">💻 Computer & Internet Use</h4>
                            <ul class="text-sm text-purple-800 space-y-1">
                                <li>• Computers are for academic purposes only</li>
                                <li>• No downloading of unauthorized software</li>
                                <li>• Respect copyright laws and licensing agreements</li>
                                <li>• Log out when finished using computers</li>
                                <li>• Report technical issues to library staff</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Library Services -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Library Services</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <h4 class="font-medium text-gray-900">📚 Available Services</h4>
                            <ul class="text-sm text-gray-700 space-y-1">
                                <li>• Book borrowing and returning</li>
                                <li>• Book renewal (online and in-person)</li>
                                <li>• Computer and internet access</li>
                                <li>• Printing and photocopying</li>
                                <li>• Research assistance</li>
                                <li>• Study spaces and quiet areas</li>
                            </ul>
                        </div>
                        <div class="space-y-3">
                            <h4 class="font-medium text-gray-900">🕒 Library Hours</h4>
                            <ul class="text-sm text-gray-700 space-y-1">
                                <li>• Monday - Friday: 8:00 AM - 10:00 PM</li>
                                <li>• Saturday: 9:00 AM - 6:00 PM</li>
                                <li>• Sunday: 10:00 AM - 5:00 PM</li>
                                <li>• Closed on public holidays</li>
                                <li>• Extended hours during exam periods</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Contact Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">📞 Library Staff</h4>
                            <ul class="text-sm text-gray-700 space-y-1">
                                <li>• Head Librarian: (555) 123-4567</li>
                                <li>• Circulation Desk: (555) 123-4568</li>
                                <li>• Reference Desk: (555) 123-4569</li>
                                <li>• Email: library@ums.com</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">📍 Location</h4>
                            <ul class="text-sm text-gray-700 space-y-1">
                                <li>• Main Library Building</li>
                                <li>• Ground Floor & First Floor</li>
                                <li>• Room 101 (Circulation Desk)</li>
                                <li>• Room 102 (Reference Section)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Important Notes -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Important Notes</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>All library rules are subject to change. Students will be notified of any updates.</li>
                                <li>Violation of library rules may result in suspension of borrowing privileges.</li>
                                <li>Students are responsible for all materials borrowed on their account.</li>
                                <li>Report any suspicious behavior or security concerns to library staff immediately.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="flex justify-center space-x-4">
                <a href="{{ route('student.library.index') }}" 
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Browse Library
                </a>
                <a href="{{ route('student.library.my-books') }}" 
                   class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    My Books
                </a>
                <a href="{{ route('student.dashboard') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</x-student-layout>
