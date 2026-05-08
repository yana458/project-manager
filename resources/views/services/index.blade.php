<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Servicios</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Consulta el catálogo de servicios, su estado y su uso en clientes y proyectos.
                </p>
            </div>

            @can('services.create')
                <a href="{{ route('services.create') }}" class="rounded-xl bg-gray-900 px-4 py-2 text-sm text-white hover:bg-black">
                    Crear servicio
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
                <form method="GET" action="{{ route('services.index') }}" class="space-y-3">
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
                        <div class="md:col-span-2">
                            <label class="mb-1 block text-sm font-medium text-gray-700">Buscar</label>
                            <input
                                type="text"
                                name="search"
                                value="{{ $search }}"
                                placeholder="Buscar por nombre o descripción"
                                class="w-full rounded-xl border-gray-200 bg-white"
                            >
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Categoría</label>
                            <select name="category" class="w-full rounded-xl border-gray-200 bg-white">
                                <option value="">Todas las categorías</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" @selected($category === $cat)>
                                        {{ $cat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Subcategoría</label>
                            <select name="sub_category" class="w-full rounded-xl border-gray-200 bg-white">
                                <option value="">Todas las subcategorías</option>
                                @foreach($subCategories as $sub)
                                    <option value="{{ $sub }}" @selected($subCategory === $sub)>
                                        {{ $sub }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Estado</label>
                            <select name="is_active" class="w-full rounded-xl border-gray-200 bg-white">
                                <option value="">Todos los estados</option>
                                <option value="1" @selected($status === '1')>Activo</option>
                                <option value="0" @selected($status === '0')>Inactivo</option>
                            </select>
                        </div>

                        <div class="md:col-span-2 flex items-end justify-between gap-2">
                            <div class="text-sm text-gray-500">
                                {{ $services->total() }} servicio{{ $services->total() === 1 ? '' : 's' }} encontrado{{ $services->total() === 1 ? '' : 's' }}
                            </div>

                            <div class="flex gap-2">
                                <a href="{{ route('services.index') }}"
                                   class="rounded-xl border px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    Limpiar filtros
                                </a>

                                <button type="submit" class="rounded-xl bg-gray-900 px-4 py-2 text-sm text-white hover:bg-black">
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
                            <th class="text-left p-3">Servicio</th>
                            <th class="text-left p-3">Categoría</th>
                            <th class="text-left p-3">Subcategoría</th>
                            <th class="text-left p-3">Actividad</th>
                            <th class="text-left p-3">Estado</th>
                            <th class="text-right p-3">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($services as $service)
                            <tr class="border-t">
                                <td class="p-3">
                                    <div class="font-medium text-gray-900">{{ $service->name }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ $service->description ?: 'Sin descripción' }}
                                    </div>
                                </td>

                                <td class="p-3 text-gray-700">
                                    {{ $service->category ?: '—' }}
                                </td>

                                <td class="p-3 text-gray-700">
                                    {{ $service->sub_category ?: '—' }}
                                </td>

                                <td class="p-3">
                                    <div class="flex flex-wrap gap-2">
                                        <span class="rounded-full bg-violet-50 px-2.5 py-1 text-xs font-semibold text-violet-700">
                                            {{ $service->clients_count }} cliente{{ $service->clients_count === 1 ? '' : 's' }}
                                        </span>

                                        <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">
                                            {{ $service->projects_count }} proyecto{{ $service->projects_count === 1 ? '' : 's' }}
                                        </span>

                                        @if($service->clients_count === 0 && $service->projects_count === 0)
                                            <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600">
                                                Sin uso
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <td class="p-3">
                                    @if($service->is_active)
                                        <span class="rounded-full bg-green-100 px-2 py-1 text-xs">activo</span>
                                    @else
                                        <span class="rounded-full bg-gray-100 px-2 py-1 text-xs">inactivo</span>
                                    @endif
                                </td>

                                <td class="p-3 text-right space-x-2">
                                    @can('services.view')
                                        <a class="underline text-gray-700 hover:text-black" href="{{ route('services.show', $service) }}">
                                            Ver
                                        </a>
                                    @endcan

                                    @can('services.edit')
                                        <a class="underline text-gray-700 hover:text-black" href="{{ route('services.edit', $service) }}">
                                            Editar
                                        </a>
                                    @endcan

                                    @can('services.deactivate')
                                        @if($service->is_active)
                                            <form class="inline" method="POST" action="{{ route('services.destroy', $service) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    class="underline text-red-700 hover:text-red-800"
                                                    onclick="return confirm('¿Desactivar este servicio?')">
                                                    Desactivar
                                                </button>
                                            </form>
                                        @else
                                            <form class="inline" method="POST" action="{{ route('services.restore', $service) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button
                                                    class="underline text-green-700 hover:text-green-800"
                                                    onclick="return confirm('¿Activar este servicio?')">
                                                    Activar
                                                </button>
                                            </form>
                                        @endif
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-6 text-center text-gray-400 text-sm">
                                    No se han encontrado servicios.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="p-4 border-t">
                    {{ $services->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>