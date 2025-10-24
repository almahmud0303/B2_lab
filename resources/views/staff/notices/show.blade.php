<x-staff-layout>
    <x-slot name="header">{{ $notice->title }}</x-slot>
    <div class="py-6">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <div class="mb-4"><span class="text-sm text-gray-500">Posted: {{ $notice->created_at->format('F d, Y') }}</span></div>
                <div class="prose max-w-none">{!! nl2br(e($notice->content)) !!}</div>
                <div class="mt-6 pt-6 border-t">
                    <a href="{{ route('staff.notices.index') }}" class="text-green-600 hover:underline">← Back to Notices</a>
                </div>
            </div>
        </div>
    </div>
</x-staff-layout>