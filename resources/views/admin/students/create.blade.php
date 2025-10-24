<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Add New Student') }}
            </h2>
            <a href="{{ route('admin.students.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Students
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.students.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Personal Information -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900">Personal Information</h3>
                                
                                <div>
                                    <x-input-label for="name" :value="__('Full Name')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="email" :value="__('Email')" />
                                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="phone" :value="__('Phone Number')" />
                                    <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')" />
                                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="date_of_birth" :value="__('Date of Birth')" />
                                    <x-text-input id="date_of_birth" class="block mt-1 w-full" type="date" name="date_of_birth" :value="old('date_of_birth')" />
                                    <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="gender" :value="__('Gender')" />
                                    <select id="gender" name="gender" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="address" :value="__('Address')" />
                                    <textarea id="address" name="address" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('address') }}</textarea>
                                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                                </div>
                            </div>

                            <!-- Academic Information -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900">Academic Information</h3>
                                
                                <div>
                                    <x-input-label for="student_id" :value="__('Student ID')" />
                                    <x-text-input id="student_id" class="block mt-1 w-full" type="text" name="student_id" :value="old('student_id')" required />
                                    <x-input-error :messages="$errors->get('student_id')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="admission_number" :value="__('Admission Number')" />
                                    <x-text-input id="admission_number" class="block mt-1 w-full" type="text" name="admission_number" :value="old('admission_number')" required />
                                    <x-input-error :messages="$errors->get('admission_number')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="department_id" :value="__('Department')" />
                                    <select id="department_id" name="department_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">Select Department</option>
                                        @foreach(\App\Models\Department::where('is_active', true)->get() as $department)
                                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="admission_date" :value="__('Admission Date')" />
                                    <x-text-input id="admission_date" class="block mt-1 w-full" type="date" name="admission_date" :value="old('admission_date', now()->format('Y-m-d'))" required />
                                    <x-input-error :messages="$errors->get('admission_date')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="academic_year" :value="__('Academic Year')" />
                                    <select id="academic_year" name="academic_year" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">Select Academic Year</option>
                                        <option value="1st" {{ old('academic_year') == '1st' ? 'selected' : '' }}>1st Year</option>
                                        <option value="2nd" {{ old('academic_year') == '2nd' ? 'selected' : '' }}>2nd Year</option>
                                        <option value="3rd" {{ old('academic_year') == '3rd' ? 'selected' : '' }}>3rd Year</option>
                                        <option value="4th" {{ old('academic_year') == '4th' ? 'selected' : '' }}>4th Year</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('academic_year')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="semester" :value="__('Semester')" />
                                    <select id="semester" name="semester" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">Select Semester</option>
                                        <option value="1st" {{ old('semester') == '1st' ? 'selected' : '' }}>1st Semester</option>
                                        <option value="2nd" {{ old('semester') == '2nd' ? 'selected' : '' }}>2nd Semester</option>
                                        <option value="3rd" {{ old('semester') == '3rd' ? 'selected' : '' }}>3rd Semester</option>
                                        <option value="4th" {{ old('semester') == '4th' ? 'selected' : '' }}>4th Semester</option>
                                        <option value="5th" {{ old('semester') == '5th' ? 'selected' : '' }}>5th Semester</option>
                                        <option value="6th" {{ old('semester') == '6th' ? 'selected' : '' }}>6th Semester</option>
                                        <option value="7th" {{ old('semester') == '7th' ? 'selected' : '' }}>7th Semester</option>
                                        <option value="8th" {{ old('semester') == '8th' ? 'selected' : '' }}>8th Semester</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('semester')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="password" :value="__('Password')" />
                                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                </div>

                                <div>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-600">Active Status</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Guardian Information -->
                        <div class="mt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Guardian Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="guardian_name" :value="__('Guardian Name')" />
                                    <x-text-input id="guardian_name" class="block mt-1 w-full" type="text" name="guardian_name" :value="old('guardian_name')" />
                                    <x-input-error :messages="$errors->get('guardian_name')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="guardian_phone" :value="__('Guardian Phone')" />
                                    <x-text-input id="guardian_phone" class="block mt-1 w-full" type="tel" name="guardian_phone" :value="old('guardian_phone')" />
                                    <x-input-error :messages="$errors->get('guardian_phone')" class="mt-2" />
                                </div>

                                <div class="md:col-span-2">
                                    <x-input-label for="guardian_address" :value="__('Guardian Address')" />
                                    <textarea id="guardian_address" name="guardian_address" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('guardian_address') }}</textarea>
                                    <x-input-error :messages="$errors->get('guardian_address')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Profile Image -->
                        <div class="mt-6">
                            <x-input-label for="profile_image" :value="__('Profile Image')" />
                            <input id="profile_image" class="block mt-1 w-full" type="file" name="profile_image" accept="image/*" />
                            <x-input-error :messages="$errors->get('profile_image')" class="mt-2" />
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.students.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                                Cancel
                            </a>
                            <x-primary-button>
                                {{ __('Create Student') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
