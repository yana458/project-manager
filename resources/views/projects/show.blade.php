<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Project detail</h2>
            <a href="{{ route('projects.index') }}" class="text-center rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                Back
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
                        <div><strong>Name:</strong> {{ $project->name }}</div>
                        <div><strong>Client:</strong> {{ $project->client->name ?? '-' }}</div>
                        <div><strong>Description:</strong> {{ $project->description ?: '-' }}</div>

                        <div>
                            <strong>Status:</strong>
                            @if($project->status === 'active')
                                <span class="ml-2 rounded-full bg-green-100 px-2 py-1 text-xs">active</span>
                            @elseif($project->status === 'paused')
                                <span class="ml-2 rounded-full bg-yellow-100 px-2 py-1 text-xs">paused</span>
                            @else
                                <span class="ml-2 rounded-full bg-gray-100 px-2 py-1 text-xs">finished</span>
                            @endif
                        </div>

                        <div><strong>Start date:</strong> {{ $project->start_date?->format('Y-m-d') ?? '-' }}</div>
                        <div><strong>End date:</strong> {{ $project->end_date?->format('Y-m-d') ?? '-' }}</div>
                        <div><strong>Created by:</strong> {{ $project->creator->name ?? '-' }}</div>
                    </div>

                    <div class="flex flex-col gap-2 min-w-[180px]">
                        @can('projects.edit')
                            <a href="{{ route('projects.edit', $project) }}"
                            class="text-center rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                                Edit
                            </a>
                        @endcan

                        @can('projects.status.change')
                            @if($project->status === 'active')
                                <form method="POST" action="{{ route('projects.pause', $project) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="w-full rounded-xl border border-yellow-200 px-4 py-2 text-sm text-yellow-700"
                                            onclick="return confirm('Pause this project?')">
                                        Pause
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('projects.finish', $project) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="w-full rounded-xl border border-gray-300 px-4 py-2 text-sm text-gray-700"
                                            onclick="return confirm('Finish this project?')">
                                        Finish
                                    </button>
                                </form>
                            @elseif($project->status === 'paused')
                                <form method="POST" action="{{ route('projects.activate', $project) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="w-full rounded-xl border border-green-200 px-4 py-2 text-sm text-green-700"
                                            onclick="return confirm('Reactivate this project?')">
                                        Reactivate
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('projects.finish', $project) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="w-full rounded-xl border border-gray-300 px-4 py-2 text-sm text-gray-700"
                                            onclick="return confirm('Finish this project?')">
                                        Finish
                                    </button>
                                </form>
                            @endif
                        @endcan
                    </div>
                </div>

                <div class="mt-6 border-t pt-4">
                    <h3 class="text-base font-semibold text-gray-900">URLs</h3>

                    <div class="mt-3 space-y-2">
                        <div>
                            <strong>Repository:</strong>
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
                            <strong>Production:</strong>
                            @if($project->production_url)
                                <a href="{{ $project->production_url }}" target="_blank" class="underline text-blue-600">
                                    {{ $project->production_url }}
                                </a>
                            @else
                                -
                            @endif
                        </div>

                        <div>
                            <strong>Docs:</strong>
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
                    <h3 class="text-base font-semibold text-gray-900">Project services</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        This section will be added in the project_services integration.
                    </p>
                </div>

                <div class="mt-6 border-t pt-4">
                    <h3 class="text-base font-semibold text-gray-900">Project team</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        This section will be added in the project_team integration.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>