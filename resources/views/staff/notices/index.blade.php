<x-staff-layout>
    <x-slot name="header">Notices</x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto">
            @foreach($notices as $notice)
                <div class="bg-white p-6 rounded-lg shadow-sm mb-4">
                    <h3 class="text-xl font-bold">{{ $notice->title }}</h3>
                    <p class="text-gray-600 mt-2">{{ Str::limit($notice->content, 200) }}</p>
                    <div class="mt-4 flex justify-between items-center">
                        <span class="text-sm text-gray-500">{{ $notice->created_at->diffForHumans() }}</span>
                        <a href="{{ route('staff.notices.show', $notice->id) }}" class="text-green-600 hover:underline">Read More →</a>
                    </div>
                </div>
            @endforeach
            <div class="mt-4">{{ $notices->links() }}</div>
        </div>
    </div>
</x-staff-layout>