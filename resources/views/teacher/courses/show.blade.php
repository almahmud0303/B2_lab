<x-teacher-layout>
    <x-slot name="header">{{ $course->title }}</x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white p-6 rounded-lg shadow-sm mb-4">
                <h2 class="text-2xl font-bold mb-4">Course Details</h2>
                <p><strong>Code:</strong> {{ $course->course_code }}</p>
                <p><strong>Credits:</strong> {{ $course->credits }}</p>
                <p><strong>Year:</strong> {{ $course->academic_year }}</p>
                <p><strong>Semester:</strong> {{ $course->semester }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <h3 class="text-xl font-bold mb-4">Enrolled Students</h3>
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">Name</th>
                            <th class="text-left py-2">Student ID</th>
                            <th class="text-left py-2">Enrollment Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                            <tr class="border-b">
                                <td class="py-2">{{ $student->user->name }}</td>
                                <td class="py-2">{{ $student->student_id }}</td>
                                <td class="py-2">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Enrolled
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-teacher-layout>