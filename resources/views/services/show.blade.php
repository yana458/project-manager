<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detalle del servicio</h2>
            <a href="{{ route('services.index') }}" class="text-center rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
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
                        <div class="text-2xl font-semibold text-gray-900">
                            {{ $service->name }}
                        </div>

                        <div class="flex flex-wrap gap-2">
                            @if($service->category)
                                <span class="rounded-full bg-violet-50 px-2.5 py-1 text-xs font-semibold text-violet-700">
                                    {{ $service->category }}
                                </span>
                            @endif

                            @if($service->sub_category)
                                <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">
                                    {{ $service->sub_category }}
                                </span>
                            @endif

                            @if($service->is_active)
                                <span class="rounded-full bg-green-100 px-2 py-1 text-xs">activo</span>
                            @else
                                <span class="rounded-full bg-gray-100 px-2 py-1 text-xs">inactivo</span>
                            @endif
                        </div>

                        <div class="pt-1 text-xs text-gray-400">
                            Creado: {{ $service->created_at?->format('d/m/Y H:i') ?? '-' }}
                            &middot;
                            Actualizado: {{ $service->updated_at?->diffForHumans() ?? '-' }}
                        </div>
                    </div>

                    <div class="flex flex-col gap-2 min-w-[180px]">
                        @can('services.edit')
                            <a href="{{ route('services.edit', $service) }}"
                               class="text-center rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                                Editar
                            </a>
                        @endcan

                        @can('services.deactivate')
                            @if($service->is_active)
                                <form method="POST" action="{{ route('services.destroy', $service) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="w-full rounded-xl border border-red-200 px-4 py-2 text-sm text-red-700"
                                            onclick="return confirm('¿Desactivar este servicio?')">
                                        Desactivar
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('services.restore', $service) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="w-full rounded-xl border border-green-200 px-4 py-2 text-sm text-green-700"
                                            onclick="return confirm('¿Activar este servicio?')">
                                        Activar
                                    </button>
                                </form>
                            @endif
                        @endcan
                    </div>
                </div>

                <div class="mt-6 border-t pt-4">
                    <h3 class="text-base font-semibold text-gray-900">Resumen</h3>

                    <div class="mt-3 grid grid-cols-1 gap-3 md:grid-cols-3">
                        <div class="rounded-xl border p-4">
                            <div class="text-sm text-gray-500">Clientes que lo tienen contratado</div>
                            <div class="mt-2 text-2xl font-semibold text-gray-900">
                                {{ $service->clients_count ?? 0 }}
                            </div>
                        </div>

                        <div class="rounded-xl border p-4">
                            <div class="text-sm text-gray-500">Proyectos donde se usa</div>
                            <div class="mt-2 text-2xl font-semibold text-gray-900">
                                {{ $service->projects_count ?? 0 }}
                            </div>
                        </div>

                        <div class="rounded-xl border p-4">
                            <div class="text-sm text-gray-500">Estado actual</div>
                            <div class="mt-2 text-lg font-semibold text-gray-900">
                                {{ $service->is_active ? 'Activo' : 'Inactivo' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 border-t pt-4">
                    <div class="text-sm font-semibold mb-2 text-gray-900">Descripción</div>

                    @if($service->description)
                        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $service->description }}</p>
                    @else
                        <p class="text-sm text-gray-400 italic">No hay descripción registrada.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>