<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Student: ' . $student->user->name) }}
            </h2>
            <a href="{{ route('admin.students.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Students
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.students.update', $student) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Personal Information -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900 pb-2 border-b">Personal Information</h3>
                                
                                <div>
                                    <x-input-label for="name" :value="__('Full Name *')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $student->user->name)" required autofocus />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="email" :value="__('Email *')" />
                                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $student->user->email)" required />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="phone" :value="__('Phone Number')" />
                                    <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone', $student->user->phone)" />
                                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="date_of_birth" :value="__('Date of Birth')" />
                                    <x-text-input id="date_of_birth" class="block mt-1 w-full" type="date" name="date_of_birth" :value="old('date_of_birth', $student->user->date_of_birth?->format('Y-m-d'))" />
                                    <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="gender" :value="__('Gender')" />
                                    <select id="gender" name="gender" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender', $student->user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', $student->user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender', $student->user->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="address" :value="__('Address')" />
                                    <textarea id="address" name="address" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('address', $student->user->address) }}</textarea>
                                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                                </div>
                            </div>

                            <!-- Academic Information -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900 pb-2 border-b">Academic Information</h3>
                                
                                <div>
                                    <x-input-label for="student_id" :value="__('Student ID *')" />
                                    <x-text-input id="student_id" class="block mt-1 w-full" type="text" name="student_id" :value="old('student_id', $student->student_id)" required />
                                    <x-input-error :messages="$errors->get('student_id')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="admission_number" :value="__('Admission Number *')" />
                                    <x-text-input id="admission_number" class="block mt-1 w-full" type="text" name="admission_number" :value="old('admission_number', $student->admission_number)" required />
                                    <x-input-error :messages="$errors->get('admission_number')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="department_id" :value="__('Department *')" />
                                    <select id="department_id" name="department_id" required class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">Select Department</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" {{ old('department_id', $student->department_id) == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }} ({{ $department->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="admission_date" :value="__('Admission Date *')" />
                                    <x-text-input id="admission_date" class="block mt-1 w-full" type="date" name="admission_date" :value="old('admission_date', $student->admission_date?->format('Y-m-d'))" required />
                                    <x-input-error :messages="$errors->get('admission_date')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="academic_year" :value="__('Academic Year *')" />
                                    <select id="academic_year" name="academic_year" required class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">Select Year</option>
                                        <option value="1st" {{ old('academic_year', $student->academic_year) == '1st' ? 'selected' : '' }}>1st Year</option>
                                        <option value="2nd" {{ old('academic_year', $student->academic_year) == '2nd' ? 'selected' : '' }}>2nd Year</option>
                                        <option value="3rd" {{ old('academic_year', $student->academic_year) == '3rd' ? 'selected' : '' }}>3rd Year</option>
                                        <option value="4th" {{ old('academic_year', $student->academic_year) == '4th' ? 'selected' : '' }}>4th Year</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('academic_year')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="semester" :value="__('Semester * (2 per year)')" />
                                    <select id="semester" name="semester" required class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">Select Semester</option>
                                        <optgroup label="1st Year">
                                            <option value="1st" {{ old('semester', $student->semester) == '1st' ? 'selected' : '' }}>1st Semester</option>
                                            <option value="2nd" {{ old('semester', $student->semester) == '2nd' ? 'selected' : '' }}>2nd Semester</option>
                                        </optgroup>
                                        <optgroup label="2nd Year">
                                            <option value="3rd" {{ old('semester', $student->semester) == '3rd' ? 'selected' : '' }}>3rd Semester</option>
                                            <option value="4th" {{ old('semester', $student->semester) == '4th' ? 'selected' : '' }}>4th Semester</option>
                                        </optgroup>
                                        <optgroup label="3rd Year">
                                            <option value="5th" {{ old('semester', $student->semester) == '5th' ? 'selected' : '' }}>5th Semester</option>
                                            <option value="6th" {{ old('semester', $student->semester) == '6th' ? 'selected' : '' }}>6th Semester</option>
                                        </optgroup>
                                        <optgroup label="4th Year">
                                            <option value="7th" {{ old('semester', $student->semester) == '7th' ? 'selected' : '' }}>7th Semester</option>
                                            <option value="8th" {{ old('semester', $student->semester) == '8th' ? 'selected' : '' }}>8th Semester</option>
                                        </optgroup>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">Each year has 2 semesters</p>
                                    <x-input-error :messages="$errors->get('semester')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="status" :value="__('Status *')" />
                                    <select id="status" name="status" required class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="active" {{ old('status', $student->status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $student->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="graduated" {{ old('status', $student->status) == 'graduated' ? 'selected' : '' }}>Graduated</option>
                                        <option value="suspended" {{ old('status', $student->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Guardian Information -->
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900 pb-2 border-b">Guardian Information</h3>
                                
                                <div>
                                    <x-input-label for="guardian_name" :value="__('Guardian Name')" />
                                    <x-text-input id="guardian_name" class="block mt-1 w-full" type="text" name="guardian_name" :value="old('guardian_name', $student->guardian_name)" />
                                    <x-input-error :messages="$errors->get('guardian_name')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="guardian_phone" :value="__('Guardian Phone')" />
                                    <x-text-input id="guardian_phone" class="block mt-1 w-full" type="tel" name="guardian_phone" :value="old('guardian_phone', $student->guardian_phone)" />
                                    <x-input-error :messages="$errors->get('guardian_phone')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="guardian_address" :value="__('Guardian Address')" />
                                    <textarea id="guardian_address" name="guardian_address" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('guardian_address', $student->guardian_address) }}</textarea>
                                    <x-input-error :messages="$errors->get('guardian_address')" class="mt-2" />
                                </div>
                            </div>

                            <!-- Security & Settings -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900 pb-2 border-b">Security & Settings</h3>
                                
                                <div>
                                    <x-input-label for="password" :value="__('New Password (leave blank to keep current)')" />
                                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" autocomplete="new-password" />
                                    <p class="text-xs text-gray-500 mt-1">Minimum 8 characters</p>
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="password_confirmation" :value="__('Confirm New Password')" />
                                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" autocomplete="new-password" />
                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                </div>

                                <div class="pt-4">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $student->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-600">Account is Active</span>
                                    </label>
                                    <p class="text-xs text-gray-500 mt-1">Uncheck to disable student login</p>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-between mt-8 pt-6 border-t">
                            <a href="{{ route('admin.students.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
                                Cancel
                            </a>
                            <div class="flex space-x-2">
                                <x-primary-button class="bg-blue-600 hover:bg-blue-700">
                                    {{ __('Update Student Information') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-update semester options based on selected year
        document.getElementById('academic_year').addEventListener('change', function() {
            const semester = document.getElementById('semester');
            const year = this.value;
            
            // Clear current selection
            semester.value = '';
            
            // Highlight appropriate semester group
            if (year) {
                const yearNum = parseInt(year.replace(/\D/g, ''));
                const semStart = (yearNum - 1) * 2 + 1;
                
                // You can add visual hints here if needed
                console.log(`Year ${yearNum} has semesters ${semStart} and ${semStart + 1}`);
            }
        });
    </script>
</x-app-layout>
