<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Dashboard de gestión</h2>
            <p class="mt-1 text-sm text-gray-500">
                Resumen general de clientes, proyectos, usuarios y servicios.
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto grid max-w-7xl gap-6 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                @include('dashboards.partials.stat-card', ['title' => 'Clientes', 'value' => $stats['clients_total']])
                @include('dashboards.partials.stat-card', ['title' => 'Clientes activos', 'value' => $stats['clients_active']])
                @include('dashboards.partials.stat-card', ['title' => 'Usuarios activos', 'value' => $stats['users_active']])
                @include('dashboards.partials.stat-card', ['title' => 'Servicios activos', 'value' => $stats['services_total']])
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                @include('dashboards.partials.stat-card', ['title' => 'Proyectos activos', 'value' => $stats['projects_active']])
                @include('dashboards.partials.stat-card', ['title' => 'Proyectos pausados', 'value' => $stats['projects_paused']])
                @include('dashboards.partials.stat-card', ['title' => 'Proyectos finalizados', 'value' => $stats['projects_finished']])
                @include('dashboards.partials.stat-card', ['title' => 'Servicios pendientes', 'value' => $stats['project_services_pending']])
            </div>

            @component('dashboards.partials.section-card', ['title' => 'Acciones rápidas'])
                @include('dashboards.partials.quick-actions', ['actions' => $actions])
            @endcomponent

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                @component('dashboards.partials.section-card', ['title' => 'Últimos proyectos'])
                    @include('dashboards.partials.project-list', ['projects' => $recentProjects])
                @endcomponent

                @component('dashboards.partials.section-card', ['title' => 'Próximos vencimientos'])
                    @include('dashboards.partials.deadlines-list', ['items' => $upcomingDeadlines])
                @endcomponent
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                @component('dashboards.partials.section-card', ['title' => 'Clientes sin servicios'])
                    @if($clientsWithoutServices->isEmpty())
                        <p class="text-sm text-gray-500">Todos los clientes tienen servicios contratados.</p>
                    @else
                        <div class="space-y-2">
                            @foreach($clientsWithoutServices as $client)
                                <div class="rounded-xl border p-3">
                                    <div class="font-medium text-gray-900">{{ $client->name }}</div>
                                    <div class="text-sm text-gray-600">{{ $client->company ?: '-' }}</div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endcomponent

                @component('dashboards.partials.section-card', ['title' => 'Proyectos sin equipo'])
                    @if($projectsWithoutTeam->isEmpty())
                        <p class="text-sm text-gray-500">Todos los proyectos tienen equipo asignado.</p>
                    @else
                        <div class="space-y-2">
                            @foreach($projectsWithoutTeam as $project)
                                <div class="rounded-xl border p-3">
                                    <div class="font-medium text-gray-900">{{ $project->name }}</div>
                                    <div class="text-sm text-gray-600">{{ $project->client->name ?? '-' }}</div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endcomponent
            </div>
        </div>
    </div>
</x-app-layout>