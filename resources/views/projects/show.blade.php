<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Project detail</h2>
            <a href="{{ route('projects.index') }}" class="text-center rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                Back
            </a>
        </div>
    </x-slot>

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
                    </div>

                    <div class="flex flex-col gap-2 min-w-[180px]">
                        @if(auth()->user()->canEditProjectInstance($project))
                            <a href="{{ route('projects.edit', $project) }}"
                               class="text-center rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                                Edit
                            </a>
                        @endif

                        @if(auth()->user()->canChangeProjectStatusInstance($project))
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
                        @endif
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

                    @if($project->users->count())
                        <div class="mt-3 space-y-3">
                            @foreach($project->users as $member)
                                <div class="rounded-xl border p-4">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $member->name }}</div>
                                            <div class="text-sm text-gray-600">{{ $member->email }}</div>

                                            <div class="mt-1 text-sm text-gray-600">
                                                <strong>Department:</strong> {{ ucfirst($member->department) }}
                                            </div>

                                            <div class="text-sm text-gray-600">
                                                <strong>Project role:</strong>
                                                @if($member->hasAnyRole(['superadmin', 'admin']))
                                                    Manager
                                                    <span class="ml-2 inline-flex rounded-full bg-purple-100 px-2 py-1 text-xs text-purple-700">
                                                        fixed
                                                    </span>
                                                @else
                                                    {{ ucfirst($member->pivot->project_role) }}
                                                @endif
                                            </div>

                                            <div class="text-sm text-gray-600">
                                                <strong>Global role:</strong>
                                                {{ ucfirst($member->getRoleNames()->first() ?? '-') }}
                                            </div>
                                        </div>

                                        @if(auth()->user()->canManageProjectTeamInstance($project))
                                            <form method="POST" action="{{ route('projects.team.destroy', [$project, $member]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="rounded-xl border border-red-200 px-4 py-2 text-sm text-red-700"
                                                        onclick="return confirm('Remove this user from the project?')">
                                                    Remove
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
                                                <label class="block text-sm font-medium text-gray-700">Project role</label>

                                                @if($member->hasAnyRole(['superadmin', 'admin']))
                                                    <div class="mt-1 w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2 text-sm text-gray-700">
                                                        Manager (fixed for admin/superadmin)
                                                    </div>
                                                @else
                                                    <select name="project_role" class="mt-1 w-full rounded-xl border-gray-200 bg-white">
                                                        @foreach($teamRoles as $role)
                                                            <option value="{{ $role }}" @selected($member->pivot->project_role === $role)>
                                                                {{ ucfirst($role) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @endif
                                            </div>

                                            <div class="flex items-end">
                                                @if(!$member->hasAnyRole(['superadmin', 'admin']))
                                                    <button class="rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                                                        Save changes
                                                    </button>
                                                @endif
                                            </div>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="mt-2 text-sm text-gray-500">No users assigned yet.</p>
                    @endif

                    @if(auth()->user()->canManageProjectTeamInstance($project))
                        <div class="mt-6 border-t pt-4 pb-16">
                            <h4 class="mb-3 text-sm font-semibold text-gray-900">Add team member</h4>

                            <form method="POST"
                                  action="{{ route('projects.team.store', $project) }}"
                                  class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                @csrf

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">User</label>
                                    <select name="user_id" class="mt-1 w-full rounded-xl border-gray-200 bg-white" required>
                                        <option value="">Select a user</option>
                                        @foreach($assignableUsers as $userOption)
                                            <option value="{{ $userOption->id }}">
                                                {{ $userOption->name }} - {{ $userOption->email }} - {{ ucfirst($userOption->department) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Project role</label>
                                    <select name="project_role" class="mt-1 w-full rounded-xl border-gray-200 bg-white" required>
                                        @foreach($teamRoles as $role)
                                            <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Admin and superadmin will always be assigned as manager automatically.
                                    </p>
                                </div>

                                <div class="flex items-end">
                                    <button class="rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                                        Add to project
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