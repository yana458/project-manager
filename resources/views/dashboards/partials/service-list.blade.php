@if($services->isEmpty())
    <p class="text-sm text-gray-500">No hay servicios asignados.</p>
@else
    <div class="space-y-3">
        @foreach($services as $projectService)
            <div class="rounded-xl border p-4">
                <div class="font-medium text-gray-900">
                    {{ $projectService->service->name ?? 'Servicio' }}
                </div>

                <div class="mt-1 text-sm text-gray-600">
                    Proyecto: {{ $projectService->project->name ?? '-' }}
                </div>

                <div class="mt-1 text-sm text-gray-600">
                    Cliente: {{ $projectService->project->client->name ?? '-' }}
                </div>

                <div class="mt-2 flex flex-wrap gap-2 text-xs">
                    <span class="rounded-full bg-gray-100 px-2 py-1">
                        {{ $projectService->status }}
                    </span>

                    @if($projectService->due_date)
                        <span class="rounded-full bg-gray-100 px-2 py-1">
                            Límite: {{ $projectService->due_date->format('Y-m-d') }}
                        </span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif