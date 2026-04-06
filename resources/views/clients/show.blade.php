<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detalle del cliente</h2>
            <a href="{{ route('clients.index') }}" class="text-center rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-6 pb-24">
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

            @if($errors->any())
                <div class="mb-4 rounded-xl bg-red-50 p-4 text-sm text-red-800">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow rounded-xl p-6 overflow-visible">
                <div class="flex items-start justify-between gap-4">
                    <div class="space-y-2">
                        <div><strong>Nombre:</strong> {{ $client->name }}</div>
                        <div><strong>Empresa:</strong> {{ $client->company ?: '-' }}</div>
                        <div><strong>Teléfono:</strong> {{ $client->phone ?: '-' }}</div>
                        <div><strong>Email:</strong> {{ $client->email ?: '-' }}</div>
                        <div><strong>Dirección:</strong> {{ $client->address ?: '-' }}</div>
                        <div><strong>NIF/CIF:</strong> {{ $client->tax_id ?: '-' }}</div>
                        <div><strong>WhatsApp:</strong> {{ $client->whatsapp ?: '-' }}</div>

                        <div>
                            <strong>Web:</strong>
                            @if($client->website_url)
                                <a href="{{ $client->website_url }}" target="_blank" class="underline text-blue-600">
                                    {{ $client->website_url }}
                                </a>
                            @else
                                -
                            @endif
                        </div>

                        <div class="pt-2">
                            <strong>Estado:</strong>
                            @if($client->is_active)
                                <span class="ml-2 rounded-full bg-green-100 px-2 py-1 text-xs">activo</span>
                            @else
                                <span class="ml-2 rounded-full bg-gray-100 px-2 py-1 text-xs">inactivo</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col gap-2 min-w-[180px]">
                        @can('clients.edit')
                            <a href="{{ route('clients.edit', $client) }}"
                               class="text-center rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                                Editar
                            </a>
                        @endcan

                        @can('clients.deactivate')
                            @if($client->is_active)
                                <form method="POST" action="{{ route('clients.deactivate', $client) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="w-full rounded-xl border border-red-200 px-4 py-2 text-sm text-red-700"
                                            onclick="return confirm('¿Desactivar este cliente?')">
                                        Desactivar
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('clients.activate', $client) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="w-full rounded-xl border border-green-200 px-4 py-2 text-sm text-green-700"
                                            onclick="return confirm('¿Activar este cliente?')">
                                        Activar
                                    </button>
                                </form>
                            @endif
                        @endcan
                    </div>
                </div>

                <div class="mt-6 border-t pt-4">
                    <h3 class="text-base font-semibold text-gray-900">Redes sociales</h3>

                    @php
                        $social = $client->social_links ?? [];
                    @endphp

                    @if(!empty($social))
                        <div class="mt-3 space-y-2">
                            @foreach($social as $platform => $url)
                                <div>
                                    <strong>{{ ucfirst($platform) }}:</strong>
                                    <a href="{{ $url }}" target="_blank" class="underline text-blue-600">
                                        {{ $url }}
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="mt-2 text-sm text-gray-500">No hay redes sociales registradas.</p>
                    @endif
                </div>

                <div class="mt-6 border-t pt-4">
                    <h3 class="text-base font-semibold text-gray-900">Notas</h3>
                    <p class="mt-2 text-sm text-gray-700 whitespace-pre-line">
                        {{ $client->notes ?: 'Sin notas.' }}
                    </p>
                </div>

                <div class="mt-6 border-t pt-4">
                    <h3 class="text-base font-semibold text-gray-900">Proyectos</h3>

                    @if($client->projects->count())
                        <div class="mt-3 space-y-3">
                            @foreach($client->projects as $project)
                                <div class="rounded-xl border p-4 flex items-center justify-between gap-4">
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $project->name }}</div>
                                        <div class="text-sm text-gray-600">
                                            Estado:
                                            <span class="ml-1 rounded-full bg-gray-100 px-2 py-1 text-xs">
                                                {{ $project->status }}
                                            </span>
                                        </div>
                                    </div>

                                    @if(Route::has('projects.show'))
                                        <a href="{{ route('projects.show', $project) }}"
                                           class="rounded-xl border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            Ver
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="mt-2 text-sm text-gray-500">Todavía no hay proyectos asociados.</p>
                    @endif
                </div>

                <div id="servicios-contratados" class="mt-6 border-t pt-4 pb-16">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Servicios contratados</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Servicios que tiene contratados actualmente este cliente.
                            </p>
                        </div>
                    </div>

                    @if($client->services->count())
                        <div class="mt-4 space-y-3">
                            @foreach($client->services as $service)
                                <div class="rounded-xl border p-4 flex items-start justify-between gap-4">
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $service->name }}</div>

                                        <div class="mt-1 flex flex-wrap gap-2">
                                            <span class="rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-700">
                                                {{ $service->category }}
                                            </span>

                                            @if($service->sub_category)
                                                <span class="rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-700">
                                                    {{ $service->sub_category }}
                                                </span>
                                            @endif

                                            @if($service->is_active)
                                                <span class="rounded-full bg-green-100 px-2 py-1 text-xs text-green-700">
                                                    activo
                                                </span>
                                            @else
                                                <span class="rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-700">
                                                    inactivo
                                                </span>
                                            @endif
                                        </div>

                                        <p class="mt-2 text-sm text-gray-600">
                                            {{ $service->description ?: 'Sin descripción.' }}
                                        </p>
                                    </div>

                                    @can('client_services.manage')
                                        <form method="POST"
                                              action="{{ route('clients.services.destroy', [$client, $service]) }}">
                                            @csrf
                                            @method('DELETE')

                                            <button class="rounded-xl border border-red-200 px-4 py-2 text-sm text-red-700 hover:bg-red-50"
                                                    onclick="return confirm('¿Quitar este servicio del cliente?')">
                                                Quitar
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="mt-3 text-sm text-gray-500">Todavía no hay servicios contratados.</p>
                    @endif

                    @can('client_services.manage')
                        <div class="mt-6 border-t pt-4">
                            <h4 class="mb-3 text-sm font-semibold text-gray-900">Añadir servicio contratado</h4>

                            <form method="POST"
                                  action="{{ route('clients.services.store', $client) }}"
                                  class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                @csrf

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Servicio</label>
                                    <select name="service_id"
                                            class="mt-1 w-full rounded-xl border-gray-200 bg-white"
                                            required>
                                        <option value="">Selecciona un servicio</option>
                                        @foreach($availableServices as $serviceOption)
                                            <option value="{{ $serviceOption->id }}" @selected(old('service_id') == $serviceOption->id)>
                                                {{ $serviceOption->name }} - {{ $serviceOption->category }}
                                                @if($serviceOption->sub_category)
                                                    - {{ $serviceOption->sub_category }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="flex items-end">
                                    <button class="rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                                        Añadir servicio
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>