<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Projects</h2>

            @can('projects.create')
                <a href="{{ route('projects.create') }}" class="rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                    Create project
                </a>
            @endcan
        </div>
    </x-slot>

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

            <div class="bg-white shadow rounded-xl p-4 mb-4">
                <form method="GET" action="{{ route('projects.index') }}" class="space-y-3">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="md:col-span-2">
                            <input
                                type="text"
                                name="search"
                                value="{{ $search }}"
                                placeholder="Search by project or client"
                                class="w-full rounded-xl border-gray-200 bg-white"
                            >
                        </div>

                        <div>
                            <select name="status" class="w-full rounded-xl border-gray-200 bg-white">
                                <option value="">All statuses</option>
                                @foreach($statuses as $projectStatus)
                                    <option value="{{ $projectStatus }}" @selected($status === $projectStatus)>
                                        {{ ucfirst($projectStatus) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="my_projects" value="1" class="rounded" @checked($myOnly)>
                            <span>My projects only</span>
                        </label>

                        <button class="rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                            Search
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow rounded-xl overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="text-left p-3">Name</th>
                            <th class="text-left p-3">Client</th>
                            <th class="text-left p-3">Company</th>
                            <th class="text-left p-3">Status</th>
                            <th class="text-left p-3">Start date</th>
                            <th class="text-left p-3">End date</th>
                            <th class="text-right p-3">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($projects as $project)
                            <tr class="border-t">
                                <td class="p-3">{{ $project->name }}</td>
                                <td class="p-3">{{ $project->client->name ?? '-' }}</td>
                                <td class="p-3">{{ $project->client->company ?? '-' }}</td>
                                <td class="p-3">
                                    @if($project->status === 'active')
                                        <span class="rounded-full bg-green-100 px-2 py-1 text-xs">active</span>
                                    @elseif($project->status === 'paused')
                                        <span class="rounded-full bg-yellow-100 px-2 py-1 text-xs">paused</span>
                                    @else
                                        <span class="rounded-full bg-gray-100 px-2 py-1 text-xs">finished</span>
                                    @endif
                                </td>
                                <td class="p-3">{{ $project->start_date?->format('Y-m-d') ?? '-' }}</td>
                                <td class="p-3">{{ $project->end_date?->format('Y-m-d') ?? '-' }}</td>

                                <td class="p-3 text-right space-x-2">
                                    @can('projects.view')
                                        <a class="underline" href="{{ route('projects.show', $project) }}">View</a>
                                    @endcan

                                    @can('projects.edit')
                                        <a class="underline" href="{{ route('projects.edit', $project) }}">Edit</a>
                                    @endcan

                                   @can('projects.status.change')
                                    @if($project->status === 'active')
                                        <form class="inline" method="POST" action="{{ route('projects.pause', $project) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button
                                                class="underline text-yellow-700"
                                                onclick="return confirm('Pause this project?')">
                                                Pause
                                            </button>
                                        </form>

                                        <form class="inline" method="POST" action="{{ route('projects.finish', $project) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button
                                                class="underline text-gray-700"
                                                onclick="return confirm('Finish this project?')">
                                                Finish
                                            </button>
                                        </form>
                                    @elseif($project->status === 'paused')
                                        <form class="inline" method="POST" action="{{ route('projects.activate', $project) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button
                                                class="underline text-green-700"
                                                onclick="return confirm('Reactivate this project?')">
                                                Reactivate
                                            </button>
                                        </form>

                                        <form class="inline" method="POST" action="{{ route('projects.finish', $project) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button
                                                class="underline text-gray-700"
                                                onclick="return confirm('Finish this project?')">
                                                Finish
                                            </button>
                                        </form>
                                    @endif
                                @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-6 text-center text-gray-500">
                                    No projects found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="p-4">
                    {{ $projects->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>