<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Teacher: ' . $teacher->user->name) }}
            </h2>
            <a href="{{ route('admin.teachers.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Teachers
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.teachers.update', $teacher) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Personal Information -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900">Personal Information</h3>
                                
                                <div>
                                    <x-input-label for="name" :value="__('Full Name')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $teacher->user->name)" required autofocus />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="email" :value="__('Email')" />
                                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $teacher->user->email)" required />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="phone" :value="__('Phone Number')" />
                                    <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone', $teacher->user->phone)" />
                                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="date_of_birth" :value="__('Date of Birth')" />
                                    <x-text-input id="date_of_birth" class="block mt-1 w-full" type="date" name="date_of_birth" :value="old('date_of_birth', $teacher->user->date_of_birth?->format('Y-m-d'))" />
                                    <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="gender" :value="__('Gender')" />
                                    <select id="gender" name="gender" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender', $teacher->user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', $teacher->user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender', $teacher->user->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="address" :value="__('Address')" />
                                    <textarea id="address" name="address" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('address', $teacher->user->address) }}</textarea>
                                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                                </div>
                            </div>

                            <!-- Professional Information -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900">Professional Information</h3>
                                
                                <div>
                                    <x-input-label for="employee_id" :value="__('Employee ID')" />
                                    <x-text-input id="employee_id" class="block mt-1 w-full" type="text" name="employee_id" :value="old('employee_id', $teacher->employee_id)" required />
                                    <x-input-error :messages="$errors->get('employee_id')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="department_id" :value="__('Department')" />
                                    <select id="department_id" name="department_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">Select Department</option>
                                        @foreach(\App\Models\Department::where('is_active', true)->get() as $department)
                                            <option value="{{ $department->id }}" {{ old('department_id', $teacher->department_id) == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="designation" :value="__('Designation')" />
                                    <select id="designation" name="designation" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">Select Designation</option>
                                        <option value="Professor" {{ old('designation', $teacher->designation) == 'Professor' ? 'selected' : '' }}>Professor</option>
                                        <option value="Associate Professor" {{ old('designation', $teacher->designation) == 'Associate Professor' ? 'selected' : '' }}>Associate Professor</option>
                                        <option value="Assistant Professor" {{ old('designation', $teacher->designation) == 'Assistant Professor' ? 'selected' : '' }}>Assistant Professor</option>
                                        <option value="Lecturer" {{ old('designation', $teacher->designation) == 'Lecturer' ? 'selected' : '' }}>Lecturer</option>
                                        <option value="Teaching Assistant" {{ old('designation', $teacher->designation) == 'Teaching Assistant' ? 'selected' : '' }}>Teaching Assistant</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('designation')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="date_of_joining" :value="__('Date of Joining')" />
                                    <x-text-input id="date_of_joining" class="block mt-1 w-full" type="date" name="date_of_joining" :value="old('date_of_joining', $teacher->joining_date?->format('Y-m-d'))" />
                                    <x-input-error :messages="$errors->get('date_of_joining')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="qualifications" :value="__('Qualifications')" />
                                    <textarea id="qualifications" name="qualifications" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="e.g., PhD in Computer Science, M.Tech in Information Technology">{{ old('qualifications', $teacher->qualifications) }}</textarea>
                                    <x-input-error :messages="$errors->get('qualifications')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="password" :value="__('New Password (leave blank to keep current)')" />
                                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" />
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="password_confirmation" :value="__('Confirm New Password')" />
                                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" />
                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                </div>

                                <div>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $teacher->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-600">Active Status</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Profile Image -->
                        <div class="mt-6">
                            <x-input-label for="profile_image" :value="__('Profile Image')" />
                            @if($teacher->user->profile_image)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $teacher->user->profile_image) }}" alt="Current profile image" class="h-20 w-20 rounded-full object-cover">
                                    <p class="text-sm text-gray-500 mt-1">Current profile image</p>
                                </div>
                            @endif
                            <input id="profile_image" class="block mt-1 w-full" type="file" name="profile_image" accept="image/*" />
                            <x-input-error :messages="$errors->get('profile_image')" class="mt-2" />
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.teachers.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                                Cancel
                            </a>
                            <x-primary-button>
                                {{ __('Update Teacher') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
