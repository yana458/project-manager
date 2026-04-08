@if($projects->isEmpty())
    <p class="text-sm text-gray-500">No hay proyectos para mostrar.</p>
@else
    <div class="space-y-3">
        @foreach($projects as $project)
            <div class="rounded-xl border p-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="font-medium text-gray-900">{{ $project->name }}</div>
                        <div class="text-sm text-gray-600">
                            {{ $project->client->name ?? 'Sin cliente' }}
                        </div>
                    </div>

                    @if(Route::has('projects.show'))
                        <a href="{{ route('projects.show', $project) }}"
                           class="rounded-xl border px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            Ver
                        </a>
                    @endif
                </div>

                <div class="mt-3 flex flex-wrap gap-2 text-xs">
                    @if($project->status === 'active')
                        <span class="rounded-full bg-green-100 px-2 py-1">activo</span>
                    @elseif($project->status === 'paused')
                        <span class="rounded-full bg-yellow-100 px-2 py-1">pausado</span>
                    @else
                        <span class="rounded-full bg-gray-100 px-2 py-1">finalizado</span>
                    @endif

                    @if($project->end_date)
                        <span class="rounded-full bg-gray-100 px-2 py-1">
                            Fin: {{ $project->end_date->format('Y-m-d') }}
                        </span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif