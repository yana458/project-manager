<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Mi dashboard</h2>
            <p class="mt-1 text-sm text-gray-500">
                Resumen de tus proyectos, servicios asignados y próximas fechas importantes.
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto grid max-w-7xl gap-6 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                @include('dashboards.partials.stat-card', ['title' => 'Mis proyectos', 'value' => $stats['my_projects_total']])
                @include('dashboards.partials.stat-card', ['title' => 'Proyectos activos', 'value' => $stats['my_projects_active']])
                @include('dashboards.partials.stat-card', ['title' => 'Servicios pendientes', 'value' => $stats['my_pending_services']])
                @include('dashboards.partials.stat-card', ['title' => 'Próximos vencimientos', 'value' => $stats['upcoming_deadlines']])
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                @include('dashboards.partials.stat-card', ['title' => 'Proyectos pausados', 'value' => $stats['my_projects_paused']])
                @include('dashboards.partials.stat-card', ['title' => 'Servicios bloqueados', 'value' => $stats['my_blocked_services']])
                @include('dashboards.partials.stat-card', ['title' => 'Proyectos vencidos', 'value' => $stats['overdue_projects']])
            </div>

            @component('dashboards.partials.section-card', ['title' => 'Acciones rápidas'])
                @include('dashboards.partials.quick-actions', ['actions' => $actions])
            @endcomponent

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                @component('dashboards.partials.section-card', ['title' => 'Mis proyectos'])
                    @include('dashboards.partials.project-list', ['projects' => $myProjects->take(8)])
                @endcomponent

                @component('dashboards.partials.section-card', ['title' => 'Próximos vencimientos'])
                    @include('dashboards.partials.deadlines-list', ['items' => $myUpcomingDeadlines])
                @endcomponent
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                @component('dashboards.partials.section-card', ['title' => 'Servicios asignados'])
                    @include('dashboards.partials.service-list', ['services' => $myAssignedServices])
                @endcomponent

                @component('dashboards.partials.section-card', ['title' => 'Avisos'])
                    <div class="space-y-3">
                        @if($myOverdueProjects->isEmpty() && $stats['my_blocked_services'] === 0)
                            <p class="text-sm text-gray-500">No tienes avisos urgentes ahora mismo.</p>
                        @endif

                        @foreach($myOverdueProjects as $project)
                            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                                El proyecto <strong>{{ $project->name }}</strong> tiene una fecha de fin vencida.
                            </div>
                        @endforeach

                        @if($stats['my_blocked_services'] > 0)
                            <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800">
                                Tienes {{ $stats['my_blocked_services'] }} servicio{{ $stats['my_blocked_services'] === 1 ? '' : 's' }} bloqueado{{ $stats['my_blocked_services'] === 1 ? '' : 's' }} asignado{{ $stats['my_blocked_services'] === 1 ? '' : 's' }}.
                            </div>
                        @endif
                    </div>
                @endcomponent
            </div>
        </div>
    </div>
</x-app-layout>