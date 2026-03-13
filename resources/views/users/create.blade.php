<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create user</h2>
            <a href="{{ route('users.index') }}" class="text-center rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">Back</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-xl p-6">
                <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <input
                            name="name"
                            value="{{ old('name') }}"
                            class="mt-1 w-full rounded-xl border-gray-200 bg-white"
                            required
                        >
                        @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            class="mt-1 w-full rounded-xl border-gray-200 bg-white"
                            required
                        >
                        @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Department</label>
                        <select
                            name="department"
                            class="mt-1 w-full rounded-xl border-gray-200 bg-white"
                            required
                        >
                            @foreach($departments as $dep)
                                <option value="{{ $dep }}" @selected(old('department','development') === $dep)>
                                    {{ ucfirst($dep) }}
                                </option>
                            @endforeach
                        </select>
                        @error('department') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Role</label>
                        @php
                        $rolesAllowed = auth()->user()->hasRole('superadmin')
                            ? ['admin','senior','junior','intern']
                            : ['senior','junior','intern'];
                        @endphp

                        <select name="role" class="mt-1 w-full rounded-xl border-gray-200 bg-white" required>
                            @foreach($rolesAllowed as $r)
                                <option value="{{ $r }}" @selected(old('role','junior') === $r)>{{ ucfirst($r) }}</option>
                            @endforeach
                        </select>
                        @error('role') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Password</label>
                        <input
                            name="password"
                            type="password"
                            class="mt-1 w-full rounded-xl border-gray-200 bg-white"
                            required
                        >
                        @error('password') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Confirm password</label>
                        <input
                            name="password_confirmation"
                            type="password"
                            class="mt-1 w-full rounded-xl border-gray-200 bg-white"
                            required
                        >
                    </div>

                    <div class="pt-2 flex gap-2">
                        <button class="rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                            Create
                        </button>
                        <a href="{{ route('users.index') }}" class="rounded-xl px-4 py-2 text-sm border">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>