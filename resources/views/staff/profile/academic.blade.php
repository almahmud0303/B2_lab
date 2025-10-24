<x-staff-layout>
    <x-slot name="header">University Information</x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h2 class="text-2xl font-bold mb-4">Khulna University of Engineering & Technology</h2>
                <p class="text-gray-700 mb-4">Khulna-9203, Bangladesh</p>
                @if($staff->department)
                    <h3 class="text-xl font-semibold mt-6 mb-3">{{ $staff->department->name }}</h3>
                    <p class="text-gray-600">{{ $staff->department->description }}</p>
                @endif
            </div>
        </div>
    </div>
</x-staff-layout>