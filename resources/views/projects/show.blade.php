<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detalle del proyecto</h2>
            <a href="{{ route('projects.index') }}" class="text-center rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                Volver
            </a>
        </div>
    </x-slot>

    @php
        $serviceStatusLabels = [
            'pending' => 'Pendiente',
            'in_progress' => 'En progreso',
            'completed' => 'Completado',
            'blocked' => 'Bloqueado',
        ];

        $projectRoleLabels = [
            'manager' => 'Gestor',
            'editor' => 'Editor',
            'viewer' => 'Lector',
        ];
    @endphp

    <div class="py-6 pb-32">
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
                        <div><strong>Nombre:</strong> {{ $project->name }}</div>
                        <div><strong>Cliente:</strong> {{ $project->client->name ?? '-' }}</div>
                        <div><strong>Descripción:</strong> {{ $project->description ?: '-' }}</div>

                        <div>
                            <strong>Estado:</strong>
                            @if($project->status === 'active')
                                <span class="ml-2 rounded-full bg-green-100 px-2 py-1 text-xs">activo</span>
                            @elseif($project->status === 'paused')
                                <span class="ml-2 rounded-full bg-yellow-100 px-2 py-1 text-xs">pausado</span>
                            @else
                                <span class="ml-2 rounded-full bg-gray-100 px-2 py-1 text-xs">finalizado</span>
                            @endif
                        </div>

                        <div><strong>Fecha de inicio:</strong> {{ $project->start_date?->format('Y-m-d') ?? '-' }}</div>
                        <div><strong>Fecha de fin:</strong> {{ $project->end_date?->format('Y-m-d') ?? '-' }}</div>
                    </div>

                    <div class="flex flex-col gap-2 min-w-[180px]">
                        @if(auth()->user()->canEditProjectInstance($project))
                            <a href="{{ route('projects.edit', $project) }}"
                               class="text-center rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                                Editar
                            </a>
                        @endif

                        @if(auth()->user()->canChangeProjectStatusInstance($project))
                            @if($project->status === 'active')
                                <form method="POST" action="{{ route('projects.pause', $project) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="w-full rounded-xl border border-yellow-200 px-4 py-2 text-sm text-yellow-700"
                                            onclick="return confirm('¿Pausar este proyecto?')">
                                        Pausar
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('projects.finish', $project) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="w-full rounded-xl border border-gray-300 px-4 py-2 text-sm text-gray-700"
                                            onclick="return confirm('¿Finalizar este proyecto?')">
                                        Finalizar
                                    </button>
                                </form>
                            @elseif($project->status === 'paused')
                                <form method="POST" action="{{ route('projects.activate', $project) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="w-full rounded-xl border border-green-200 px-4 py-2 text-sm text-green-700"
                                            onclick="return confirm('¿Reactivar este proyecto?')">
                                        Reactivar
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('projects.finish', $project) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="w-full rounded-xl border border-gray-300 px-4 py-2 text-sm text-gray-700"
                                            onclick="return confirm('¿Finalizar este proyecto?')">
                                        Finalizar
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>

                <div class="mt-6 border-t pt-4">
                    <h3 class="text-base font-semibold text-gray-900">URLs</h3>

                    <div class="mt-3 space-y-2">
                        <div>
                            <strong>Repositorio:</strong>
                            @if($project->repo_url)
                                <a href="{{ $project->repo_url }}" target="_blank" class="underline text-blue-600">
                                    {{ $project->repo_url }}
                                </a>
                            @else
                                -
                            @endif
                        </div>

                        <div>
                            <strong>Staging:</strong>
                            @if($project->staging_url)
                                <a href="{{ $project->staging_url }}" target="_blank" class="underline text-blue-600">
                                    {{ $project->staging_url }}
                                </a>
                            @else
                                -
                            @endif
                        </div>

                        <div>
                            <strong>Producción:</strong>
                            @if($project->production_url)
                                <a href="{{ $project->production_url }}" target="_blank" class="underline text-blue-600">
                                    {{ $project->production_url }}
                                </a>
                            @else
                                -
                            @endif
                        </div>

                        <div>
                            <strong>Documentación:</strong>
                            @if($project->docs_url)
                                <a href="{{ $project->docs_url }}" target="_blank" class="underline text-blue-600">
                                    {{ $project->docs_url }}
                                </a>
                            @else
                                -
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-6 border-t pt-4">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Servicios del proyecto</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Servicios añadidos a este proyecto a partir de los servicios contratados por el cliente.
                            </p>
                        </div>

                        @if(auth()->user()->can('client_services.manage'))
                            <a href="{{ route('clients.show', $project->client) }}#servicios-contratados"
                               class="rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                                Ir a servicios del cliente
                            </a>
                        @elseif(auth()->user()->can('clients.view'))
                            <a href="{{ route('clients.show', $project->client) }}#servicios-contratados"
                               class="rounded-xl border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                Ver servicios del cliente
                            </a>
                        @endif
                    </div>

                    @if($project->projectServices->count())
                        <div class="mt-4 space-y-4">
                            @foreach($project->projectServices as $projectService)
                                <div class="rounded-xl border p-4">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <div class="font-medium text-gray-900">
                                                {{ $projectService->service->name }}
                                            </div>

                                            <div class="mt-1 flex flex-wrap gap-2">
                                                <span class="rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-700">
                                                    {{ $projectService->service->category }}
                                                </span>

                                                @if($projectService->service->sub_category)
                                                    <span class="rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-700">
                                                        {{ $projectService->service->sub_category }}
                                                    </span>
                                                @endif

                                                <span class="rounded-full bg-blue-100 px-2 py-1 text-xs text-blue-700">
                                                    {{ $serviceStatusLabels[$projectService->status] ?? $projectService->status }}
                                                </span>
                                            </div>

                                            <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-gray-600">
                                                <div>
                                                    <strong>Fecha de inicio:</strong>
                                                    {{ $projectService->start_date?->format('Y-m-d') ?? '-' }}
                                                </div>

                                                <div>
                                                    <strong>Fecha límite:</strong>
                                                    {{ $projectService->due_date?->format('Y-m-d') ?? '-' }}
                                                </div>

                                                <div>
                                                    <strong>Responsable:</strong>
                                                    {{ $projectService->assignedUser->name ?? 'Sin asignar' }}
                                                </div>

                                                <div>
                                                    <strong>Estado del servicio:</strong>
                                                    {{ $serviceStatusLabels[$projectService->status] ?? $projectService->status }}
                                                </div>
                                            </div>

                                            <div class="mt-3 text-sm text-gray-600">
                                                <strong>Notas:</strong>
                                                <div class="mt-1 whitespace-pre-line">
                                                    {{ $projectService->notes ?: 'Sin notas.' }}
                                                </div>
                                            </div>
                                        </div>

                                        @can('project_services.manage')
                                            <form method="POST"
                                                  action="{{ route('projects.services.destroy', [$project, $projectService]) }}">
                                                @csrf
                                                @method('DELETE')

                                                <button class="rounded-xl border border-red-200 px-4 py-2 text-sm text-red-700 hover:bg-red-50"
                                                        onclick="return confirm('¿Quitar este servicio del proyecto?')">
                                                    Quitar
                                                </button>
                                            </form>
                                        @endcan
                                    </div>

                                    @can('project_services.manage')
                                        <form method="POST"
                                              action="{{ route('projects.services.update', [$project, $projectService]) }}"
                                              class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                                            @csrf
                                            @method('PATCH')

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Fecha de inicio</label>
                                                <input type="date"
                                                       name="start_date"
                                                       value="{{ old('start_date', optional($projectService->start_date)->format('Y-m-d')) }}"
                                                       class="mt-1 w-full rounded-xl border-gray-200 bg-white">
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Fecha límite</label>
                                                <input type="date"
                                                       name="due_date"
                                                       value="{{ old('due_date', optional($projectService->due_date)->format('Y-m-d')) }}"
                                                       class="mt-1 w-full rounded-xl border-gray-200 bg-white">
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Estado</label>
                                                <select name="status" class="mt-1 w-full rounded-xl border-gray-200 bg-white">
                                                    @foreach($projectServiceStatuses as $status)
                                                        <option value="{{ $status }}" @selected(old('status', $projectService->status) === $status)>
                                                            {{ $serviceStatusLabels[$status] ?? $status }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Responsable</label>
                                                <select name="assigned_user_id" class="mt-1 w-full rounded-xl border-gray-200 bg-white">
                                                    <option value="">Sin asignar</option>
                                                    @foreach($assignableServiceUsers as $member)
                                                        <option value="{{ $member->id }}"
                                                            @selected(old('assigned_user_id', $projectService->assigned_user_id) == $member->id)>
                                                            {{ $member->name }}
                                                            @if($member->pivot?->project_role)
                                                                - {{ $projectRoleLabels[$member->pivot->project_role] ?? $member->pivot->project_role }}
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="md:col-span-2">
                                                <label class="block text-sm font-medium text-gray-700">Notas</label>
                                                <textarea name="notes"
                                                          rows="3"
                                                          class="mt-1 w-full rounded-xl border-gray-200 bg-white">{{ old('notes', $projectService->notes) }}</textarea>
                                            </div>

                                            <div class="md:col-span-2 flex justify-end">
                                                <button class="rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                                                    Guardar cambios del servicio
                                                </button>
                                            </div>
                                        </form>
                                    @endcan
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="mt-3 text-sm text-gray-500">Todavía no hay servicios añadidos a este proyecto.</p>
                    @endif

                    @can('project_services.manage')
                        <div class="mt-6 border-t pt-4 pb-16">
                            <h4 class="mb-3 text-sm font-semibold text-gray-900">Añadir servicio al proyecto</h4>

                            @if($availableProjectServices->count())
                                <form method="POST"
                                      action="{{ route('projects.services.store', $project) }}"
                                      class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @csrf

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Servicio</label>
                                        <select name="service_id"
                                                class="mt-1 w-full rounded-xl border-gray-200 bg-white"
                                                required>
                                            <option value="">Selecciona un servicio</option>
                                            @foreach($availableProjectServices as $serviceOption)
                                                <option value="{{ $serviceOption->id }}" @selected(old('service_id') == $serviceOption->id)>
                                                    {{ $serviceOption->name }} - {{ $serviceOption->category }}
                                                    @if($serviceOption->sub_category)
                                                        - {{ $serviceOption->sub_category }}
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        <p class="mt-1 text-xs text-gray-500">
                                            Solo aparecen servicios contratados por el cliente y que todavía no están añadidos al proyecto.
                                        </p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Responsable</label>
                                        <select name="assigned_user_id" class="mt-1 w-full rounded-xl border-gray-200 bg-white">
                                            <option value="">Sin asignar</option>
                                            @foreach($assignableServiceUsers as $member)
                                                <option value="{{ $member->id }}" @selected(old('assigned_user_id') == $member->id)>
                                                    {{ $member->name }}
                                                    @if($member->pivot?->project_role)
                                                        - {{ $projectRoleLabels[$member->pivot->project_role] ?? $member->pivot->project_role }}
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Fecha de inicio</label>
                                        <input type="date"
                                               name="start_date"
                                               value="{{ old('start_date') }}"
                                               class="mt-1 w-full rounded-xl border-gray-200 bg-white">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Fecha límite</label>
                                        <input type="date"
                                               name="due_date"
                                               value="{{ old('due_date') }}"
                                               class="mt-1 w-full rounded-xl border-gray-200 bg-white">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Estado</label>
                                        <select name="status" class="mt-1 w-full rounded-xl border-gray-200 bg-white" required>
                                            @foreach($projectServiceStatuses as $status)
                                                <option value="{{ $status }}" @selected(old('status', 'pending') === $status)>
                                                    {{ $serviceStatusLabels[$status] ?? $status }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700">Notas</label>
                                        <textarea name="notes"
                                                  rows="3"
                                                  class="mt-1 w-full rounded-xl border-gray-200 bg-white">{{ old('notes') }}</textarea>
                                    </div>

                                    <div class="md:col-span-2 flex justify-end">
                                        <button class="rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                                            Añadir servicio al proyecto
                                        </button>
                                    </div>
                                </form>
                            @else
                                <div class="mt-3 rounded-xl border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800">
                                    No hay servicios disponibles para añadir a este proyecto. Primero tienes que contratar al menos un servicio para este cliente.
                                </div>

                                @if(auth()->user()->can('client_services.manage'))
                                    <div class="mt-4">
                                        <a href="{{ route('clients.show', $project->client) }}#servicios-contratados"
                                           class="inline-flex rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                                            Añadir servicio contratado al cliente
                                        </a>
                                    </div>
                                @elseif(auth()->user()->can('clients.view'))
                                    <div class="mt-4">
                                        <a href="{{ route('clients.show', $project->client) }}#servicios-contratados"
                                           class="inline-flex rounded-xl border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            Ver servicios del cliente
                                        </a>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endcan
                </div>

                <div class="mt-6 border-t pt-4">
                    <h3 class="text-base font-semibold text-gray-900">Equipo del proyecto</h3>

                    @if($project->users->count())
                        <div class="mt-3 space-y-3">
                            @foreach($project->users as $member)
                                <div class="rounded-xl border p-4">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $member->name }}</div>
                                            <div class="text-sm text-gray-600">{{ $member->email }}</div>

                                            <div class="mt-1 text-sm text-gray-600">
                                                <strong>Departamento:</strong> {{ ucfirst($member->department) }}
                                            </div>

                                            <div class="text-sm text-gray-600">
                                                <strong>Rol en el proyecto:</strong>
                                                @if($member->hasAnyRole(['superadmin', 'admin']))
                                                    Gestor
                                                    <span class="ml-2 inline-flex rounded-full bg-purple-100 px-2 py-1 text-xs text-purple-700">
                                                        fijo
                                                    </span>
                                                @else
                                                    {{ $projectRoleLabels[$member->pivot->project_role] ?? $member->pivot->project_role }}
                                                @endif
                                            </div>

                                            <div class="text-sm text-gray-600">
                                                <strong>Rol global:</strong>
                                                {{ ucfirst($member->getRoleNames()->first() ?? '-') }}
                                            </div>
                                        </div>

                                        @if(auth()->user()->canManageProjectTeamInstance($project))
                                            <form method="POST" action="{{ route('projects.team.destroy', [$project, $member]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="rounded-xl border border-red-200 px-4 py-2 text-sm text-red-700"
                                                        onclick="return confirm('¿Quitar este usuario del proyecto?')">
                                                    Quitar
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    @if(auth()->user()->canManageProjectTeamInstance($project))
                                        <form method="POST"
                                              action="{{ route('projects.team.update', [$project, $member]) }}"
                                              class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3">
                                            @csrf
                                            @method('PATCH')

                                            <div class="md:col-span-2">
                                                <label class="block text-sm font-medium text-gray-700">Rol en el proyecto</label>

                                                @if($member->hasAnyRole(['superadmin', 'admin']))
                                                    <div class="mt-1 w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2 text-sm text-gray-700">
                                                        Gestor (fijo para admin/superadmin)
                                                    </div>
                                                @else
                                                    <select name="project_role" class="mt-1 w-full rounded-xl border-gray-200 bg-white">
                                                        @foreach($teamRoles as $role)
                                                            <option value="{{ $role }}" @selected($member->pivot->project_role === $role)>
                                                                {{ $projectRoleLabels[$role] ?? $role }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @endif
                                            </div>

                                            <div class="flex items-end">
                                                @if(!$member->hasAnyRole(['superadmin', 'admin']))
                                                    <button class="rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                                                        Guardar cambios
                                                    </button>
                                                @endif
                                            </div>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="mt-2 text-sm text-gray-500">Todavía no hay usuarios asignados.</p>
                    @endif

                    @if(auth()->user()->canManageProjectTeamInstance($project))
                        <div class="mt-6 border-t pt-4 pb-16">
                            <h4 class="mb-3 text-sm font-semibold text-gray-900">Añadir miembro al equipo</h4>

                            <form method="POST"
                                  action="{{ route('projects.team.store', $project) }}"
                                  class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                @csrf

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Usuario</label>
                                    <select name="user_id" class="mt-1 w-full rounded-xl border-gray-200 bg-white" required>
                                        <option value="">Selecciona un usuario</option>
                                        @foreach($assignableUsers as $userOption)
                                            <option value="{{ $userOption->id }}">
                                                {{ $userOption->name }} - {{ $userOption->email }} - {{ ucfirst($userOption->department) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Rol en el proyecto</label>
                                    <select name="project_role" class="mt-1 w-full rounded-xl border-gray-200 bg-white" required>
                                        @foreach($teamRoles as $role)
                                            <option value="{{ $role }}">{{ $projectRoleLabels[$role] ?? $role }}</option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Los usuarios admin y superadmin se asignarán siempre como gestor automáticamente.
                                    </p>
                                </div>

                                <div class="flex items-end">
                                    <button class="rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                                        Añadir al proyecto
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>