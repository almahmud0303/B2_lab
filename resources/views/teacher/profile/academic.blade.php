<x-teacher-layout>
    <x-slot name="header">University Information</x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h2 class="text-2xl font-bold mb-4">{{ $teacher->department->name }}</h2>
                @if($departmentHead)
                    <p class="text-gray-700 mb-4">Department Head: {{ $departmentHead->name }}</p>
                @endif
                <h3 class="text-xl font-semibold mt-6 mb-3">Department Teachers</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($departmentTeachers as $t)
                        <div class="border p-4 rounded">
                            <p class="font-semibold">{{ $t->user->name }}</p>
                            <p class="text-sm text-gray-600">{{ $t->employee_id }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-teacher-layout>