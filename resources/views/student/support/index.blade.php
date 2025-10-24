<x-student-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Support & Help Center') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Quick Help -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <a href="{{ route('student.support.knowledge-base') }}" 
                   class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-blue-500 mb-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900">Knowledge Base</h3>
                        <p class="text-sm text-gray-600">Find answers to common questions</p>
                    </div>
                </a>

                <a href="{{ route('student.support.tutorials') }}" 
                   class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-green-500 mb-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900">Video Tutorials</h3>
                        <p class="text-sm text-gray-600">Watch step-by-step guides</p>
                    </div>
                </a>

                <button onclick="toggleSupportTicket()" 
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-purple-500 mb-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900">Submit Ticket</h3>
                        <p class="text-sm text-gray-600">Get help from our support team</p>
                    </div>
                </button>

                <a href="{{ route('student.support.contact') }}" 
                   class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-yellow-500 mb-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900">Contact Us</h3>
                        <p class="text-sm text-gray-600">Get in touch directly</p>
                    </div>
                </a>
            </div>

            <!-- Support Ticket Form -->
            <div id="support-ticket-form" class="hidden bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Submit Support Ticket</h3>
                    
                    <form method="POST" action="{{ route('student.support.submit-ticket') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                                <input type="text" name="subject" id="subject" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('subject')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                                <select name="category" id="category" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Category</option>
                                    <option value="technical">Technical Support</option>
                                    <option value="academic">Academic</option>
                                    <option value="financial">Financial</option>
                                    <option value="general">General</option>
                                </select>
                                @error('category')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                            <select name="priority" id="priority" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                            @error('priority')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-6">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" id="description" rows="6" required
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Please describe your issue in detail..."></textarea>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-6">
                            <label for="attachments" class="block text-sm font-medium text-gray-700">Attachments (Optional)</label>
                            <input type="file" name="attachments[]" id="attachments" multiple
                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                   class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="text-xs text-gray-500 mt-1">Maximum 3 files, 2MB each. Supported formats: PDF, DOC, DOCX, JPG, PNG</p>
                            @error('attachments')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" onclick="toggleSupportTicket()"
                                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Submit Ticket
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Frequently Asked Questions</h3>
                    
                    <div class="space-y-6">
                        @foreach($faqCategories as $categoryKey => $category)
                            <div>
                                <h4 class="text-md font-medium text-gray-800 mb-3">{{ $category['title'] }}</h4>
                                <div class="space-y-3">
                                    @foreach($category['faqs'] as $index => $faq)
                                        <div class="border border-gray-200 rounded-lg">
                                            <button onclick="toggleFAQ('{{ $categoryKey }}_{{ $index }}')" 
                                                    class="w-full px-4 py-3 text-left flex justify-between items-center hover:bg-gray-50">
                                                <span class="text-sm font-medium text-gray-900">{{ $faq['question'] }}</span>
                                                <svg class="h-5 w-5 text-gray-500 transform transition-transform" 
                                                     id="icon-{{ $categoryKey }}_{{ $index }}" 
                                                     fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                </svg>
                                            </button>
                                            <div id="faq-{{ $categoryKey }}_{{ $index }}" class="hidden px-4 pb-3">
                                                <p class="text-sm text-gray-600">{{ $faq['answer'] }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Contact Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="flex items-center mb-4">
                                <svg class="h-5 w-5 text-gray-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Email</p>
                                    <p class="text-sm text-gray-600">{{ $contactInfo['email'] }}</p>
                                </div>
                            </div>

                            <div class="flex items-center mb-4">
                                <svg class="h-5 w-5 text-gray-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Phone</p>
                                    <p class="text-sm text-gray-600">{{ $contactInfo['phone'] }}</p>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-gray-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Hours</p>
                                    <p class="text-sm text-gray-600">{{ $contactInfo['hours'] }}</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="flex items-start">
                                <svg class="h-5 w-5 text-gray-400 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Address</p>
                                    <p class="text-sm text-gray-600">{{ $contactInfo['address'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleSupportTicket() {
            const form = document.getElementById('support-ticket-form');
            form.classList.toggle('hidden');
        }

        function toggleFAQ(id) {
            const faq = document.getElementById('faq-' + id);
            const icon = document.getElementById('icon-' + id);
            
            if (faq.classList.contains('hidden')) {
                faq.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                faq.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }
    </script>
</x-student-layout>
