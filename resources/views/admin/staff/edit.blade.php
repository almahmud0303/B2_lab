<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Staff - {{ $staff->user->name }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.staff.update', $staff->id) }}">
                    @csrf
                    @method('PUT')
                    <h3 class="text-lg font-semibold mb-4 border-b pb-2">Personal Information</h3>
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div><label class="block text-sm font-medium mb-1">Full Name *</label><input type="text" name="name" value="{{ old('name', $staff->user->name) }}" class="w-full border rounded px-3 py-2" required></div>
                        <div><label class="block text-sm font-medium mb-1">Email *</label><input type="email" name="email" value="{{ old('email', $staff->user->email) }}" class="w-full border rounded px-3 py-2" required></div>
                        <div><label class="block text-sm font-medium mb-1">Phone</label><input type="tel" name="phone" value="{{ old('phone', $staff->user->phone) }}" class="w-full border rounded px-3 py-2"></div>
                        <div><label class="block text-sm font-medium mb-1">Date of Birth</label><input type="date" name="date_of_birth" value="{{ old('date_of_birth', $staff->user->date_of_birth?->format('Y-m-d')) }}" class="w-full border rounded px-3 py-2"></div>
                        <div><label class="block text-sm font-medium mb-1">Gender</label><select name="gender" class="w-full border rounded px-3 py-2"><option value="">Select</option><option value="male" {{ $staff->user->gender == 'male' ? 'selected' : '' }}>Male</option><option value="female" {{ $staff->user->gender == 'female' ? 'selected' : '' }}>Female</option><option value="other" {{ $staff->user->gender == 'other' ? 'selected' : '' }}>Other</option></select></div>
                        <div class="col-span-2"><label class="block text-sm font-medium mb-1">Address</label><textarea name="address" rows="2" class="w-full border rounded px-3 py-2">{{ old('address', $staff->user->address) }}</textarea></div>
                    </div>
                    <h3 class="text-lg font-semibold mb-4 border-b pb-2">Employment Information</h3>
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div><label class="block text-sm font-medium mb-1">Employee ID *</label><input type="text" name="employee_id" value="{{ old('employee_id', $staff->employee_id) }}" class="w-full border rounded px-3 py-2" required></div>
                        <div><label class="block text-sm font-medium mb-1">Position *</label><select name="position" class="w-full border rounded px-3 py-2" required><option value="librarian" {{ $staff->position == 'librarian' ? 'selected' : '' }}>Librarian</option><option value="clerk" {{ $staff->position == 'clerk' ? 'selected' : '' }}>Clerk</option><option value="accountant" {{ $staff->position == 'accountant' ? 'selected' : '' }}>Accountant</option><option value="lab_assistant" {{ $staff->position == 'lab_assistant' ? 'selected' : '' }}>Lab Assistant</option><option value="office_assistant" {{ $staff->position == 'office_assistant' ? 'selected' : '' }}>Office Assistant</option><option value="other" {{ $staff->position == 'other' ? 'selected' : '' }}>Other</option></select></div>
                        <div><label class="block text-sm font-medium mb-1">Location *</label><select name="location" class="w-full border rounded px-3 py-2" required><option value="library" {{ $staff->location == 'library' ? 'selected' : '' }}>Library</option><option value="administration" {{ $staff->location == 'administration' ? 'selected' : '' }}>Administration Building</option><option value="department" {{ $staff->location == 'department' ? 'selected' : '' }}>Department</option></select></div>
                        <div><label class="block text-sm font-medium mb-1">Department</label><select name="department_id" class="w-full border rounded px-3 py-2"><option value="">None</option>@foreach($departments as $dept)<option value="{{ $dept->id }}" {{ $staff->department_id == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>@endforeach</select></div>
                        <div><label class="block text-sm font-medium mb-1">Qualification</label><input type="text" name="qualification" value="{{ old('qualification', $staff->qualification) }}" class="w-full border rounded px-3 py-2"></div>
                        <div><label class="block text-sm font-medium mb-1">Salary</label><input type="number" step="0.01" name="salary" value="{{ old('salary', $staff->salary) }}" class="w-full border rounded px-3 py-2"></div>
                        <div><label class="block text-sm font-medium mb-1">Joining Date *</label><input type="date" name="joining_date" value="{{ old('joining_date', $staff->joining_date->format('Y-m-d')) }}" class="w-full border rounded px-3 py-2" required></div>
                        <div><label class="block text-sm font-medium mb-1">Employment Type *</label><select name="employment_type" class="w-full border rounded px-3 py-2" required><option value="full_time" {{ $staff->employment_type == 'full_time' ? 'selected' : '' }}>Full Time</option><option value="part_time" {{ $staff->employment_type == 'part_time' ? 'selected' : '' }}>Part Time</option><option value="contract" {{ $staff->employment_type == 'contract' ? 'selected' : '' }}>Contract</option></select></div>
                        <div class="col-span-2"><label class="block text-sm font-medium mb-1">Responsibilities</label><textarea name="responsibilities" rows="3" class="w-full border rounded px-3 py-2">{{ old('responsibilities', $staff->responsibilities) }}</textarea></div>
                        <div class="col-span-2"><label class="flex items-center"><input type="checkbox" name="is_active" value="1" {{ $staff->is_active ? 'checked' : '' }} class="mr-2"><span class="text-sm font-medium">Active</span></label></div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('admin.staff.index') }}" class="bg-gray-200 px-6 py-2 rounded">Cancel</a>
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Update Staff</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>