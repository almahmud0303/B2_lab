<x-staff-layout>
    <x-slot name="header">Edit Profile</x-slot>
    <div class="py-6">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('staff.profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-8 border-b pb-6">
                        <h3 class="text-lg font-medium mb-4">Profile Picture</h3>
                        <div class="flex items-center space-x-6">
                            <div class="flex-shrink-0">
                                @if($staff->user->profile_image)
                                    <img id="preview" class="h-24 w-24 rounded-full object-cover" src="{{ asset('storage/' . $staff->user->profile_image) }}" alt="{{ $staff->user->name }}">
                                @else
                                    <div id="preview" class="h-24 w-24 rounded-full bg-green-500 flex items-center justify-center text-white text-2xl font-bold">{{ strtoupper(substr($staff->user->name, 0, 2)) }}</div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <input type="file" name="profile_image" id="profile_image" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-green-50 file:text-green-700 hover:file:bg-green-100" onchange="previewImage(event)">
                                <p class="mt-1 text-xs text-gray-500">JPG, PNG, GIF up to 2MB</p>
                            </div>
                        </div>
                    </div>
                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-4">Personal Information</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="block text-sm font-medium text-gray-700">Full Name</label><input type="text" name="name" value="{{ old('name', $staff->user->name) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required></div>
                            <div><label class="block text-sm font-medium text-gray-700">Email</label><input type="email" name="email" value="{{ old('email', $staff->user->email) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required></div>
                            <div><label class="block text-sm font-medium text-gray-700">Phone</label><input type="tel" name="phone" value="{{ old('phone', $staff->user->phone) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></div>
                            <div><label class="block text-sm font-medium text-gray-700">Date of Birth</label><input type="date" name="date_of_birth" value="{{ old('date_of_birth', $staff->user->date_of_birth?->format('Y-m-d')) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></div>
                            <div><label class="block text-sm font-medium text-gray-700">Gender</label><select name="gender" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"><option value="">Select</option><option value="male" {{ $staff->user->gender == 'male' ? 'selected' : '' }}>Male</option><option value="female" {{ $staff->user->gender == 'female' ? 'selected' : '' }}>Female</option><option value="other" {{ $staff->user->gender == 'other' ? 'selected' : '' }}>Other</option></select></div>
                            <div class="col-span-2"><label class="block text-sm font-medium text-gray-700">Address</label><textarea name="address" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('address', $staff->user->address) }}</textarea></div>
                        </div>
                    </div>
                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-4">Professional Information</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="block text-sm font-medium text-gray-700">Qualification</label><input type="text" name="qualification" value="{{ old('qualification', $staff->qualification) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></div>
                            <div class="col-span-2"><label class="block text-sm font-medium text-gray-700">Responsibilities</label><textarea name="responsibilities" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('responsibilities', $staff->responsibilities) }}</textarea></div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('staff.profile.index') }}" class="bg-gray-200 px-6 py-2 rounded">Cancel</a>
                        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded">Update Profile</button>
                    </div>
                </form>
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
                    if (preview.tagName === 'IMG') {
                        preview.src = e.target.result;
                    } else {
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
</x-staff-layout>