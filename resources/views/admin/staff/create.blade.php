<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Add New Staff Member</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.staff.store') }}">
                    @csrf
                    <h3 class="text-lg font-semibold mb-4 border-b pb-2">Personal Information</h3>
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div><label class="block text-sm font-medium mb-1">Full Name *</label><input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded px-3 py-2" required>@error('name')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror</div>
                        <div><label class="block text-sm font-medium mb-1">Email *</label><input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded px-3 py-2" required>@error('email')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror</div>
                        <div><label class="block text-sm font-medium mb-1">Password *</label><input type="password" name="password" class="w-full border rounded px-3 py-2" required>@error('password')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror</div>
                        <div><label class="block text-sm font-medium mb-1">Confirm Password *</label><input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2" required></div>
                        <div><label class="block text-sm font-medium mb-1">Phone</label><input type="tel" name="phone" value="{{ old('phone') }}" class="w-full border rounded px-3 py-2"></div>
                        <div><label class="block text-sm font-medium mb-1">Date of Birth</label><input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" class="w-full border rounded px-3 py-2"></div>
                        <div><label class="block text-sm font-medium mb-1">Gender</label><select name="gender" class="w-full border rounded px-3 py-2"><option value="">Select</option><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select></div>
                        <div class="col-span-2"><label class="block text-sm font-medium mb-1">Address</label><textarea name="address" rows="2" class="w-full border rounded px-3 py-2">{{ old('address') }}</textarea></div>
                    </div>
                    <h3 class="text-lg font-semibold mb-4 border-b pb-2">Employment Information</h3>
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div><label class="block text-sm font-medium mb-1">Employee ID *</label><input type="text" name="employee_id" value="{{ old('employee_id') }}" class="w-full border rounded px-3 py-2" required>@error('employee_id')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror</div>
                        <div><label class="block text-sm font-medium mb-1">Position *</label><select name="position" class="w-full border rounded px-3 py-2" required><option value="">Select Position</option><option value="librarian">Librarian</option><option value="clerk">Clerk</option><option value="accountant">Accountant</option><option value="lab_assistant">Lab Assistant</option><option value="office_assistant">Office Assistant</option><option value="other">Other</option></select>@error('position')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror</div>
                        <div><label class="block text-sm font-medium mb-1">Location *</label><select name="location" class="w-full border rounded px-3 py-2" required><option value="">Select Location</option><option value="library">Library</option><option value="administration">Administration Building</option><option value="department">Department</option></select>@error('location')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror</div>
                        <div><label class="block text-sm font-medium mb-1">Department</label><select name="department_id" class="w-full border rounded px-3 py-2"><option value="">None (for admin/library staff)</option>@foreach($departments as $dept)<option value="{{ $dept->id }}">{{ $dept->name }}</option>@endforeach</select><p class="text-xs text-gray-500 mt-1">Only for department-assigned staff</p></div>
                        <div><label class="block text-sm font-medium mb-1">Qualification</label><input type="text" name="qualification" value="{{ old('qualification') }}" class="w-full border rounded px-3 py-2"></div>
                        <div><label class="block text-sm font-medium mb-1">Salary</label><input type="number" step="0.01" name="salary" value="{{ old('salary') }}" class="w-full border rounded px-3 py-2"></div>
                        <div><label class="block text-sm font-medium mb-1">Joining Date *</label><input type="date" name="joining_date" value="{{ old('joining_date', now()->format('Y-m-d')) }}" class="w-full border rounded px-3 py-2" required></div>
                        <div><label class="block text-sm font-medium mb-1">Employment Type *</label><select name="employment_type" class="w-full border rounded px-3 py-2" required><option value="full_time">Full Time</option><option value="part_time">Part Time</option><option value="contract">Contract</option></select></div>
                        <div class="col-span-2"><label class="block text-sm font-medium mb-1">Responsibilities</label><textarea name="responsibilities" rows="3" class="w-full border rounded px-3 py-2">{{ old('responsibilities') }}</textarea></div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('admin.staff.index') }}" class="bg-gray-200 px-6 py-2 rounded">Cancel</a>
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Create Staff</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>