<x-teacher-layout>
    <x-slot name="header">My Courses</x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto">
            @foreach($courses as $course)
                <div class="bg-white p-6 rounded-lg shadow-sm mb-4">
                    <h3 class="text-xl font-bold">{{ $course->title }}</h3>
                    <p class="text-gray-600">{{ $course->course_code }} | {{ $course->credits }} Credits</p>
                    <p class="text-sm text-gray-500 mt-2">Enrolled: {{ $course->enrollments_count }} students</p>
                    <a href="{{ route('teacher.courses.show', $course->id) }}" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded">View Details</a>
                </div>
            @endforeach
        </div>
    </div>
</x-teacher-layout>