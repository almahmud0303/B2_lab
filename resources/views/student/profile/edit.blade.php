<x-student-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if(session('success'))
                        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Profile Picture Section -->
                        <div class="mb-8 border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Profile Picture</h3>
                            
                            <div class="flex items-center space-x-6">
                                <!-- Current Profile Picture -->
                                <div class="flex-shrink-0">
                                    @if($student->user->profile_image)
                                        <img id="preview" class="h-24 w-24 rounded-full object-cover" 
                                             src="{{ asset('storage/' . $student->user->profile_image) }}" 
                                             alt="{{ $student->user->name }}">
                                    @else
                                        <div id="preview" class="h-24 w-24 rounded-full bg-blue-500 flex items-center justify-center text-white text-2xl font-bold">
                                            {{ strtoupper(substr($student->user->name, 0, 2)) }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Upload Button -->
                                <div class="flex-1">
                                    <label for="profile_image" class="block text-sm font-medium text-gray-700 mb-2">
                                        Change Profile Picture
                                    </label>
                                    <input type="file" 
                                           name="profile_image" 
                                           id="profile_image" 
                                           accept="image/*"
                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                           onchange="previewImage(event)">
                                    <p class="mt-1 text-xs text-gray-500">
                                        JPG, PNG, GIF up to 2MB
                                    </p>
                                    @error('profile_image')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Personal Information -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                                    <input type="text" 
                                           name="name" 
                                           id="name" 
                                           value="{{ old('name', $student->user->name) }}"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                           required>
                                    @error('name')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                                    <input type="email" 
                                           name="email" 
                                           id="email" 
                                           value="{{ old('email', $student->user->email) }}"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                           required>
                                    @error('email')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                                    <input type="tel" 
                                           name="phone" 
                                           id="phone" 
                                           value="{{ old('phone', $student->user->phone) }}"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    @error('phone')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                                    <input type="date" 
                                           name="date_of_birth" 
                                           id="date_of_birth" 
                                           value="{{ old('date_of_birth', $student->user->date_of_birth?->format('Y-m-d')) }}"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    @error('date_of_birth')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
                                    <select name="gender" 
                                            id="gender" 
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender', $student->user->gender) === 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', $student->user->gender) === 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender', $student->user->gender) === 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('gender')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-4">
                                <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                                <textarea name="address" 
                                          id="address" 
                                          rows="3"
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('address', $student->user->address) }}</textarea>
                                @error('address')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Guardian Information -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Guardian Information</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="guardian_name" class="block text-sm font-medium text-gray-700">Guardian Name</label>
                                    <input type="text" 
                                           name="guardian_name" 
                                           id="guardian_name" 
                                           value="{{ old('guardian_name', $student->guardian_name) }}"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    @error('guardian_name')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="guardian_phone" class="block text-sm font-medium text-gray-700">Guardian Phone</label>
                                    <input type="tel" 
                                           name="guardian_phone" 
                                           id="guardian_phone" 
                                           value="{{ old('guardian_phone', $student->guardian_phone) }}"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    @error('guardian_phone')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-4">
                                <label for="guardian_address" class="block text-sm font-medium text-gray-700">Guardian Address</label>
                                <textarea name="guardian_address" 
                                          id="guardian_address" 
                                          rows="3"
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('guardian_address', $student->guardian_address) }}</textarea>
                                @error('guardian_address')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                            <a href="{{ route('student.profile.index') }}" 
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('preview');
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    // If preview is an img tag
                    if (preview.tagName === 'IMG') {
                        preview.src = e.target.result;
                    } else {
                        // If preview is a div, replace it with an img
                        const img = document.createElement('img');
                        img.id = 'preview';
                        img.className = 'h-24 w-24 rounded-full object-cover';
                        img.src = e.target.result;
                        preview.replaceWith(img);
                    }
                };
                
                reader.readAsDataURL(file);
            }
        }
    </script>
</x-student-layout>
