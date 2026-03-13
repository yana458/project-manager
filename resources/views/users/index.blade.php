<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Users</h2>

            @can('users.create')
                <a href="{{ route('users.create') }}" class="rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                    Create user
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 rounded-xl bg-green-50 p-4 text-sm text-green-800">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 rounded-xl bg-red-50 p-4 text-sm text-red-800">{{ session('error') }}</div>
            @endif

            <div class="bg-white shadow rounded-xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="text-left p-3">Name</th>
                            <th class="text-left p-3">Email</th>
                            <th class="text-left p-3">Department</th>
                            <th class="text-left p-3">Role</th>
                            <th class="text-left p-3">Status</th>
                            <th class="text-right p-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $u)
                            <tr class="border-t">
                                <td class="p-3">{{ $u->name }}</td>
                                <td class="p-3">{{ $u->email }}</td>
                                <td class="p-3">{{ $u->department }}</td>
                                <td class="p-3">{{ $u->getRoleNames()->implode(', ') ?: '-' }}</td>
                                <td class="p-3">
                                    @if($u->is_active)
                                        <span class="rounded-full bg-green-100 px-2 py-1 text-xs">active</span>
                                    @else
                                        <span class="rounded-full bg-gray-100 px-2 py-1 text-xs">inactive</span>
                                    @endif
                                </td>
                                <td class="p-3 text-right space-x-2">
                                    <a class="underline" href="{{ route('users.show', $u) }}">View</a>

                                    @can('users.edit')
                                        <a class="underline" href="{{ route('users.edit', $u) }}">Edit</a>
                                    @endcan
                                    
                                    @php
                                        $actorIsSuper = Auth::user()->hasRole('superadmin');
                                        $targetIsPrivileged = $u->hasAnyRole(['superadmin','admin']);
                                    @endphp

                                    @can('users.deactivate')
                                        {{-- Deactivate solo si: activo, no soy yo, y (no es privileged o soy superadmin) --}}
                                        @if($u->is_active && $u->id !== auth()->id() && ($actorIsSuper || !$targetIsPrivileged))
                                            <form class="inline" method="POST" action="{{ route('users.deactivate', $u) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button class="underline text-red-700" onclick="return confirm('Deactivate this user?')">
                                                    Deactivate
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Activate solo si: inactivo, y (no es privileged o soy superadmin) --}}
                                        @if(!$u->is_active && ($actorIsSuper || !$targetIsPrivileged))
                                            <form class="inline" method="POST" action="{{ route('users.activate', $u) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button class="underline text-green-700" onclick="return confirm('Activate this user?')">
                                                    Activate
                                                </button>
                                            </form>
                                        @endif
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="p-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>