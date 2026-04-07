<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Usuarios</h2>

            @can('users.create')
                <a href="{{ route('users.create') }}" class="rounded-xl bg-gray-900 px-4 py-2 text-sm text-white hover:bg-black">
                    Crear usuario
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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

            <div class="bg-white shadow rounded-xl overflow-hidden">
                <div class="border-b bg-gray-50 px-4 py-3">
                    <p class="text-sm text-gray-600">
                        Gestión de usuarios internos, roles globales y estado de acceso.
                    </p>
                </div>

                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="text-left p-3">Nombre</th>
                            <th class="text-left p-3">Correo electrónico</th>
                            <th class="text-left p-3">Departamento</th>
                            <th class="text-left p-3">Rol</th>
                            <th class="text-left p-3">Estado</th>
                            <th class="text-right p-3">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($users as $u)
                            @php
                                $actorIsSuper = auth()->user()->hasRole('superadmin');
                                $targetIsPrivileged = $u->hasAnyRole(['superadmin', 'admin']);
                            @endphp

                            <tr class="border-t">
                                <td class="p-3 font-medium text-gray-900">{{ $u->name }}</td>
                                <td class="p-3 text-gray-700">{{ $u->email }}</td>
                                <td class="p-3 text-gray-700">{{ ucfirst($u->department) }}</td>
                                <td class="p-3 text-gray-700">{{ $u->getRoleNames()->implode(', ') ?: '-' }}</td>
                                <td class="p-3">
                                    @if($u->is_active)
                                        <span class="rounded-full bg-green-100 px-2 py-1 text-xs">activo</span>
                                    @else
                                        <span class="rounded-full bg-gray-100 px-2 py-1 text-xs">inactivo</span>
                                    @endif
                                </td>

                                <td class="p-3 text-right space-x-2">
                                    <a class="underline text-gray-700 hover:text-black" href="{{ route('users.show', $u) }}">
                                        Ver
                                    </a>

                                    @can('users.edit')
                                        @if($actorIsSuper || !$targetIsPrivileged)
                                            <a class="underline text-gray-700 hover:text-black" href="{{ route('users.edit', $u) }}">
                                                Editar
                                            </a>
                                        @endif
                                    @endcan

                                    @can('users.deactivate')
                                        @if($u->is_active && $u->id !== auth()->id() && ($actorIsSuper || !$targetIsPrivileged))
                                            <form class="inline" method="POST" action="{{ route('users.deactivate', $u) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button
                                                    class="underline text-red-700 hover:text-red-800"
                                                    onclick="return confirm('¿Desactivar este usuario?')">
                                                    Desactivar
                                                </button>
                                            </form>
                                        @endif

                                        @if(!$u->is_active && ($actorIsSuper || !$targetIsPrivileged))
                                            <form class="inline" method="POST" action="{{ route('users.activate', $u) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button
                                                    class="underline text-green-700 hover:text-green-800"
                                                    onclick="return confirm('¿Activar este usuario?')">
                                                    Activar
                                                </button>
                                            </form>
                                        @endif
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr class="border-t">
                                <td colspan="6" class="p-6 text-center text-sm text-gray-500">
                                    No hay usuarios registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="p-4 border-t">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>