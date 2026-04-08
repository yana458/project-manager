<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Clientes</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Consulta clientes, su actividad y sus servicios contratados.
                </p>
            </div>

            @can('clients.create')
                <a href="{{ route('clients.create') }}" class="rounded-xl bg-gray-900 px-4 py-2 text-sm text-white hover:bg-black">
                    Crear cliente
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

            <div class="mb-4 rounded-xl bg-white p-4 shadow">
                <form method="GET" action="{{ route('clients.index') }}" class="space-y-3">
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
                        <div class="md:col-span-2">
                            <label class="mb-1 block text-sm font-medium text-gray-700">Buscar</label>
                            <input
                                type="text"
                                name="search"
                                value="{{ $search }}"
                                placeholder="Buscar por nombre, empresa, email, NIF/CIF, teléfono..."
                                class="w-full rounded-xl border-gray-200 bg-white"
                            >
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Estado</label>
                            <select name="status" class="w-full rounded-xl border-gray-200 bg-white">
                                <option value="">Todos los estados</option>
                                <option value="active" @selected($status === 'active')>Activo</option>
                                <option value="inactive" @selected($status === 'inactive')>Inactivo</option>
                            </select>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Proyectos</label>
                            <select name="projects" class="w-full rounded-xl border-gray-200 bg-white">
                                <option value="">Todos</option>
                                <option value="with" @selected($projectsFilter === 'with')>Con proyectos</option>
                                <option value="without" @selected($projectsFilter === 'without')>Sin proyectos</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Servicios contratados</label>
                            <select name="services" class="w-full rounded-xl border-gray-200 bg-white">
                                <option value="">Todos</option>
                                <option value="with" @selected($servicesFilter === 'with')>Con servicios</option>
                                <option value="without" @selected($servicesFilter === 'without')>Sin servicios</option>
                            </select>
                        </div>

                        <div class="md:col-span-2 flex items-end justify-between gap-2">
                            <div class="text-sm text-gray-500">
                                {{ $clients->total() }} cliente{{ $clients->total() === 1 ? '' : 's' }} encontrado{{ $clients->total() === 1 ? '' : 's' }}
                            </div>

                            <div class="flex gap-2">
                                <a href="{{ route('clients.index') }}"
                                   class="rounded-xl border px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    Limpiar filtros
                                </a>

                                <button class="rounded-xl bg-gray-900 px-4 py-2 text-sm text-white hover:bg-black">
                                    Buscar
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="overflow-hidden rounded-xl bg-white shadow">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="p-3 text-left">Nombre</th>
                            <th class="p-3 text-left">Empresa</th>
                            <th class="p-3 text-left">Email</th>
                            <th class="p-3 text-left">NIF/CIF</th>
                            <th class="p-3 text-left">Actividad</th>
                            <th class="p-3 text-left">Estado</th>
                            <th class="p-3 text-right">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($clients as $client)
                            <tr class="border-t">
                                <td class="p-3 font-medium text-gray-900">
                                    {{ $client->name }}
                                </td>

                                <td class="p-3 text-gray-700">
                                    {{ $client->company ?: '-' }}
                                </td>

                                <td class="p-3 text-gray-700">
                                    {{ $client->email ?: '-' }}
                                </td>

                                <td class="p-3 text-gray-700">
                                    {{ $client->tax_id ?: '-' }}
                                </td>

                                <td class="p-3">
                                    <div class="flex flex-wrap gap-2">
                                        <span class="rounded-full bg-violet-50 px-2.5 py-1 text-xs font-semibold text-violet-700">
                                            {{ $client->projects_count }} proyecto{{ $client->projects_count === 1 ? '' : 's' }}
                                        </span>

                                        <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">
                                            {{ $client->services_count }} servicio{{ $client->services_count === 1 ? '' : 's' }}
                                        </span>

                                        @if($client->projects_count === 0 && $client->services_count === 0)
                                            <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600">
                                                Sin actividad
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <td class="p-3">
                                    @if($client->is_active)
                                        <span class="rounded-full bg-green-100 px-2 py-1 text-xs">activo</span>
                                    @else
                                        <span class="rounded-full bg-gray-100 px-2 py-1 text-xs">inactivo</span>
                                    @endif
                                </td>

                                <td class="p-3 text-right space-x-2">
                                    @can('clients.view')
                                        <a class="underline text-gray-700 hover:text-black" href="{{ route('clients.show', $client) }}">
                                            Ver
                                        </a>
                                    @endcan

                                    @can('clients.edit')
                                        <a class="underline text-gray-700 hover:text-black" href="{{ route('clients.edit', $client) }}">
                                            Editar
                                        </a>
                                    @endcan

                                    @can('clients.deactivate')
                                        @if($client->is_active)
                                            <form class="inline" method="POST" action="{{ route('clients.deactivate', $client) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button
                                                    class="underline text-red-700 hover:text-red-800"
                                                    onclick="return confirm('¿Desactivar este cliente?')">
                                                    Desactivar
                                                </button>
                                            </form>
                                        @else
                                            <form class="inline" method="POST" action="{{ route('clients.activate', $client) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button
                                                    class="underline text-green-700 hover:text-green-800"
                                                    onclick="return confirm('¿Activar este cliente?')">
                                                    Activar
                                                </button>
                                            </form>
                                        @endif
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-6 text-center text-gray-500">
                                    No se han encontrado clientes.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="p-4 border-t">
                    {{ $clients->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>