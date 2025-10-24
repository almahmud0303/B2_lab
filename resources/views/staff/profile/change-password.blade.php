<x-staff-layout>
    <x-slot name="header">Change Password</x-slot>
    <div class="py-6">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('staff.profile.update-password') }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-6"><label class="block text-sm font-medium text-gray-700">Current Password</label><input type="password" name="current_password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>@error('current_password')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                    <div class="mb-6"><label class="block text-sm font-medium text-gray-700">New Password</label><input type="password" name="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>@error('password')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror<p class="mt-1 text-xs text-gray-500">Minimum 8 characters</p></div>
                    <div class="mb-6"><label class="block text-sm font-medium text-gray-700">Confirm Password</label><input type="password" name="password_confirmation" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required></div>
                    <div class="flex justify-end gap-3 pt-6 border-t">
                        <a href="{{ route('staff.profile.index') }}" class="bg-gray-200 px-6 py-2 rounded">Cancel</a>
                        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded">Change Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-staff-layout>