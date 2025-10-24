<x-staff-layout>
    <x-slot name="header">Issue New Book</x-slot>
    <div class="py-6">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('staff.book-issues.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Student</label>
                        <select name="student_id" class="w-full border rounded px-4 py-2" required>
                            <option value="">Select Student</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}">{{ $student->user->name }} ({{ $student->student_id }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Book</label>
                        <select name="book_id" class="w-full border rounded px-4 py-2" required>
                            <option value="">Select Book</option>
                            @foreach($books as $book)
                                <option value="{{ $book->id }}">{{ $book->title }} (Available: {{ $book->available_copies }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div><label class="block text-sm font-medium text-gray-700 mb-2">Issue Date</label><input type="date" name="issue_date" value="{{ now()->format('Y-m-d') }}" class="w-full border rounded px-4 py-2" required></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-2">Due Date</label><input type="date" name="due_date" value="{{ now()->addDays(14)->format('Y-m-d') }}" class="w-full border rounded px-4 py-2" required></div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" rows="3" class="w-full border rounded px-4 py-2"></textarea>
                    </div>
                    <div class="flex justify-end gap-2">
                        <a href="{{ route('staff.book-issues.index') }}" class="bg-gray-200 px-6 py-2 rounded">Cancel</a>
                        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded">Issue Book</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-staff-layout>