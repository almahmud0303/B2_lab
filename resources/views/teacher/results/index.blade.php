<x-teacher-layout>
    <x-slot name="header">Results Management</x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <div class="flex justify-between mb-4">
                    <h3 class="text-xl font-bold">Student Results</h3>
                    <div>
                        <a href="{{ route('teacher.results.index', ['status' => 'published']) }}" class="px-4 py-2 {{ request('status') == 'published' ? 'bg-blue-600 text-white' : 'bg-gray-200' }} rounded">Published</a>
                        <a href="{{ route('teacher.results.index', ['status' => 'unpublished']) }}" class="px-4 py-2 {{ request('status') == 'unpublished' ? 'bg-blue-600 text-white' : 'bg-gray-200' }} rounded">Unpublished</a>
                    </div>
                </div>
                <table class="min-w-full">
                    <thead><tr class="border-b"><th class="text-left py-2">Student</th><th class="text-left py-2">Exam</th><th class="text-left py-2">Marks</th><th class="text-left py-2">Grade</th><th class="text-left py-2">Actions</th></tr></thead>
                    <tbody>
                        @foreach($results as $result)
                            <tr class="border-b">
                                <td class="py-2">{{ $result->student->user->name }}</td>
                                <td class="py-2">{{ $result->exam->exam_name }}</td>
                                <td class="py-2">{{ $result->marks_obtained }}/{{ $result->total_marks }}</td>
                                <td class="py-2">{{ $result->grade }}</td>
                                <td class="py-2">
                                    @if($result->is_published)
                                        <form method="POST" action="{{ route('teacher.results.unpublish', $result->id) }}" class="inline">@csrf<button class="text-red-600">Unpublish</button></form>
                                    @else
                                        <form method="POST" action="{{ route('teacher.results.publish', $result->id) }}" class="inline">@csrf<button class="text-green-600">Publish</button></form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">{{ $results->links() }}</div>
            </div>
        </div>
    </div>
</x-teacher-layout>