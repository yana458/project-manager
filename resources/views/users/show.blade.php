<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">User detail</h2>
            <a href="{{ route('users.index') }}" class="text-center rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">Back</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 rounded-xl bg-green-50 p-4 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 rounded-xl bg-red-50 p-4 text-sm text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white shadow rounded-xl p-6">
                <div class="flex items-start justify-between gap-4">
                    <div class="space-y-2">
                        <div><strong>Name:</strong> {{ $user->name }}</div>
                        <div><strong>Email:</strong> {{ $user->email }}</div>
                        <div><strong>Department:</strong> {{ $user->department }}</div>
                        <div><strong>Role:</strong> {{ $user->getRoleNames()->implode(', ') ?: '-' }}</div>

                        <div class="pt-2">
                            <strong>Status:</strong>
                            @if($user->is_active)
                                <span class="ml-2 rounded-full bg-green-100 px-2 py-1 text-xs">active</span>
                            @else
                                <span class="ml-2 rounded-full bg-gray-100 px-2 py-1 text-xs">inactive</span>
                            @endif
                        </div>
                    </div>

                    {{-- Botones (derecha) --}}
                    <div class="flex flex-col gap-2 min-w-[180px]">
                        @can('users.edit')
                            <a href="{{ route('users.edit', $user) }}"
                               class="text-center rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                                Edit
                            </a>
                        @endcan

                        @can('users.deactivate')
                            {{-- Deactivate (solo si está activo y NO es tu usuario) --}}
                            @if($user->is_active && $user->id !== auth()->id())
                                <form method="POST" action="{{ route('users.deactivate', $user) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="w-full rounded-xl border border-red-200 px-4 py-2 text-sm text-red-700"
                                            onclick="return confirm('Deactivate this user?')">
                                        Deactivate
                                    </button>
                                </form>
                            @endif

                            {{-- Activate (solo si está inactivo) --}}
                            @if(!$user->is_active)
                                <form method="POST" action="{{ route('users.activate', $user) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="w-full rounded-xl border border-green-200 px-4 py-2 text-sm text-green-700"
                                            onclick="return confirm('Activate this user?')">
                                        Activate
                                    </button>
                                </form>
                            @endif
                        @endcan

                        {{-- Mensaje si es tu propio usuario --}}
                        @if($user->id === auth()->id())
                            <p class="text-xs text-gray-500 mt-1">
                                You cannot deactivate your own account.
                            </p>
                        @endif
                    </div>
                </div>

                {{-- (Opcional) bloque de cambiar rol aquí si lo quieres también en show --}}
                @can('users.role.assign')
                    @if($user->id !== auth()->id())
                        <div class="mt-6 border-t pt-4">
                            <div class="text-sm font-semibold mb-2">Change role</div>
                            <form method="POST" action="{{ route('users.role.assign', $user) }}" class="flex gap-2 items-end">
                                @csrf
                                @method('PATCH')

                                @php($currentRole = $user->getRoleNames()->first() ?? 'junior')
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Role</label>
                                    <select name="role" class="mt-1 w-full rounded-xl border-gray-200 bg-white text-sm">
                                        @foreach(['admin','senior','junior','intern'] as $r)
                                            <option value="{{ $r }}" @selected($currentRole === $r)>{{ ucfirst($r) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <button class="rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                                    Save
                                </button>
                            </form>
                        </div>
                    @endif
                @endcan
            </div>
        </div>
    </div>
</x-app-layout>