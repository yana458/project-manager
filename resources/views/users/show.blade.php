<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">User detail</h2>
            <a href="{{ route('users.index') }}" class="underline text-sm">Back</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 rounded-xl bg-green-50 p-4 text-sm text-green-800">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow rounded-xl p-6 space-y-2">
                <div><strong>Name:</strong> {{ $user->name }}</div>
                <div><strong>Email:</strong> {{ $user->email }}</div>
                <div><strong>Department:</strong> {{ $user->department }}</div>
                <div><strong>Role:</strong> {{ $user->getRoleNames()->implode(', ') ?: '-' }}</div>
                <div><strong>Status:</strong> {{ $user->is_active ? 'active' : 'inactive' }}</div>
            </div>
        </div>
    </div>
</x-app-layout>