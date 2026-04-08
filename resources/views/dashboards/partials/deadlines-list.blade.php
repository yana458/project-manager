@if($items->isEmpty())
    <p class="text-sm text-gray-500">No hay fechas próximas.</p>
@else
    <div class="space-y-3">
        @foreach($items as $project)
            <div class="rounded-xl border p-4">
                <div class="font-medium text-gray-900">{{ $project->name }}</div>
                <div class="mt-1 text-sm text-gray-600">
                    {{ $project->client->name ?? 'Sin cliente' }}
                </div>
                <div class="mt-2 text-sm text-gray-700">
                    Fecha límite: <strong>{{ $project->end_date?->format('Y-m-d') ?? '-' }}</strong>
                </div>
            </div>
        @endforeach
    </div>
@endif