<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Proyectos</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Consulta, filtra y gestiona los proyectos según tu nivel de acceso.
                </p>
            </div>

            @can('projects.create')
                <a href="{{ route('projects.create') }}" class="rounded-xl bg-gray-900 px-4 py-2 text-sm text-white hover:bg-black">
                    Crear proyecto
                </a>
            @endcan
        </div>
    </x-slot>

    @php
        $statusLabels = [
            'active' => 'Activo',
            'paused' => 'Pausado',
            'finished' => 'Finalizado',
        ];

        $projectRoleLabels = [
            'manager' => 'Manager',
            'editor' => 'Editor',
            'viewer' => 'Viewer',
        ];
    @endphp

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
                <form method="GET" action="{{ route('projects.index') }}" class="space-y-3">
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
                        <div class="md:col-span-2">
                            <label class="mb-1 block text-sm font-medium text-gray-700">Buscar</label>
                            <input
                                type="text"
                                name="search"
                                value="{{ $search }}"
                                placeholder="Buscar por proyecto o cliente"
                                class="w-full rounded-xl border-gray-200 bg-white"
                            >
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Estado</label>
                            <select name="status" class="w-full rounded-xl border-gray-200 bg-white">
                                <option value="">Todos los estados</option>
                                @foreach($statuses as $projectStatus)
                                    <option value="{{ $projectStatus }}" @selected($status === $projectStatus)>
                                        {{ $statusLabels[$projectStatus] ?? ucfirst($projectStatus) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-end">
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                <input type="checkbox" name="my_projects" value="1" class="rounded" @checked($myOnly)>
                                <span>Solo mis proyectos</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-500">
                            {{ $projects->total() }} proyecto{{ $projects->total() === 1 ? '' : 's' }} encontrado{{ $projects->total() === 1 ? '' : 's' }}
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('projects.index') }}"
                               class="rounded-xl border px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                Limpiar filtros
                            </a>

                            <button class="rounded-xl bg-gray-900 px-4 py-2 text-sm text-white hover:bg-black">
                                Buscar
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto rounded-xl bg-white shadow">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="p-3 text-left">Proyecto</th>
                            <th class="p-3 text-left">Cliente</th>
                            <th class="p-3 text-left">Estado</th>
                            <th class="p-3 text-left">Mi participación</th>
                            <th class="p-3 text-left">Inicio</th>
                            <th class="p-3 text-left">Fin</th>
                            <th class="p-3 text-right">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($projects as $project)
                            @php
                                $myAssignment = $project->users->firstWhere('id', auth()->id());
                                $myProjectRole = $myAssignment?->pivot?->project_role;
                            @endphp

                            <tr class="border-t">
                                <td class="p-3">
                                    <div class="font-medium text-gray-900">{{ $project->name }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ $project->client->company ?: 'Sin empresa' }}
                                    </div>
                                </td>

                                <td class="p-3 text-gray-700">
                                    {{ $project->client->name ?? '-' }}
                                </td>

                                <td class="p-3">
                                    @if($project->status === 'active')
                                        <span class="rounded-full bg-green-100 px-2 py-1 text-xs">activo</span>
                                    @elseif($project->status === 'paused')
                                        <span class="rounded-full bg-yellow-100 px-2 py-1 text-xs">pausado</span>
                                    @else
                                        <span class="rounded-full bg-gray-100 px-2 py-1 text-xs">finalizado</span>
                                    @endif
                                </td>

                                <td class="p-3">
                                    @if($myProjectRole)
                                        @if($myProjectRole === 'manager')
                                            <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                                Manager
                                            </span>
                                        @else
                                            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                                {{ $projectRoleLabels[$myProjectRole] ?? $myProjectRole }}
                                            </span>
                                        @endif
                                    @elseif($project->created_by === auth()->id())
                                        <span class="rounded-full bg-violet-50 px-2.5 py-1 text-xs font-semibold text-violet-700">
                                            Creador
                                        </span>
                                    @else
                                        <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600">
                                            Sin asignación
                                        </span>
                                    @endif
                                </td>

                                <td class="p-3 text-gray-700">
                                    {{ $project->start_date?->format('Y-m-d') ?? '-' }}
                                </td>

                                <td class="p-3 text-gray-700">
                                    {{ $project->end_date?->format('Y-m-d') ?? '-' }}
                                </td>

                                <td class="p-3 text-right space-x-2">
                                    @if(auth()->user()->canViewProjectInstance($project))
                                        <a class="underline text-gray-700 hover:text-black" href="{{ route('projects.show', $project) }}">
                                            Ver
                                        </a>
                                    @endif

                                    @if(auth()->user()->canEditProjectInstance($project))
                                        <a class="underline text-gray-700 hover:text-black" href="{{ route('projects.edit', $project) }}">
                                            Editar
                                        </a>
                                    @endif

                                    @if(auth()->user()->canChangeProjectStatusInstance($project))
                                        @if($project->status === 'active')
                                            <form class="inline" method="POST" action="{{ route('projects.pause', $project) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button
                                                    class="underline text-yellow-700 hover:text-yellow-800"
                                                    onclick="return confirm('¿Pausar este proyecto?')">
                                                    Pausar
                                                </button>
                                            </form>

                                            <form class="inline" method="POST" action="{{ route('projects.finish', $project) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button
                                                    class="underline text-gray-700 hover:text-black"
                                                    onclick="return confirm('¿Finalizar este proyecto?')">
                                                    Finalizar
                                                </button>
                                            </form>
                                        @elseif($project->status === 'paused')
                                            <form class="inline" method="POST" action="{{ route('projects.activate', $project) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button
                                                    class="underline text-green-700 hover:text-green-800"
                                                    onclick="return confirm('¿Reactivar este proyecto?')">
                                                    Reactivar
                                                </button>
                                            </form>

                                            <form class="inline" method="POST" action="{{ route('projects.finish', $project) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button
                                                    class="underline text-gray-700 hover:text-black"
                                                    onclick="return confirm('¿Finalizar este proyecto?')">
                                                    Finalizar
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-6 text-center text-gray-500">
                                    No se han encontrado proyectos.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="border-t p-4">
                    {{ $projects->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>