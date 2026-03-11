<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit user</h2>
            <a href="{{ route('users.show', $user) }}" class="underline text-sm">Back</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-xl p-6">
                <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <input
                            name="name"
                            value="{{ old('name', $user->name) }}"
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
                            value="{{ old('email', $user->email) }}"
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
                                <option value="{{ $dep }}" @selected(old('department', $user->department) === $dep)>
                                    {{ ucfirst($dep) }}
                                </option>
                            @endforeach
                        </select>
                        @error('department') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Role</label>

                        @php($currentRole = $user->getRoleNames()->first() ?? 'junior')

                        <select name="role"
                                class="mt-1 w-full rounded-xl border-gray-200 bg-white"
                                @disabled($user->id === auth()->id())>
                            @foreach($roles as $r)
                                <option value="{{ $r }}" @selected(old('role', $currentRole) === $r)>{{ ucfirst($r) }}</option>
                            @endforeach
                        </select>

                        @if($user->id === auth()->id())
                            <p class="text-xs text-gray-500 mt-1">You cannot change your own role.</p>
                        @endif

                        @error('role') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="pt-2 border-t"></div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            New password (optional)
                        </label>
                        <input
                            name="password"
                            type="password"
                            class="mt-1 w-full rounded-xl border-gray-200 bg-white"
                        >
                        @error('password') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        <p class="text-xs text-gray-500 mt-1">
                            Leave empty to keep the current password.
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Confirm new password</label>
                        <input
                            name="password_confirmation"
                            type="password"
                            class="mt-1 w-full rounded-xl border-gray-200 bg-white"
                        >
                    </div>

                    <div class="pt-2 flex gap-2">
                        <button class="rounded-xl bg-green-900 px-4 py-2 text-sm text-white">
                            Save
                        </button>
                        <a href="{{ route('users.show', $user) }}" class="rounded-xl px-4 py-2 text-sm border">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>