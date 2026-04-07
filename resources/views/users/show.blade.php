<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detalle de usuario</h2>
            <a href="{{ route('users.index') }}" class="text-center rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

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
                @php
                    $actorIsSuper = auth()->user()->hasRole('superadmin');
                    $targetIsSuper = $user->hasRole('superadmin');
                    $targetIsPrivileged = $user->hasAnyRole(['superadmin', 'admin']);
                    $projects = $user->projects ?? collect();

                    $statusLabel = function (?string $status): string {
                        return match($status) {
                            'active' => 'Activo',
                            'paused' => 'Pausado',
                            'completed' => 'Finalizado',
                            'cancelled' => 'Cancelado',
                            default => ucfirst(str_replace('_', ' ', $status ?? 'sin estado')),
                        };
                    };
                @endphp

                <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                    <div class="space-y-3">
                        <div><strong>Nombre:</strong> {{ $user->name }}</div>
                        <div><strong>Email:</strong> {{ $user->email }}</div>
                        <div><strong>Departamento:</strong> {{ ucfirst($user->department) }}</div>
                        <div><strong>Rol:</strong> {{ $user->getRoleNames()->implode(', ') ?: '-' }}</div>

                        <div class="pt-2">
                            <strong>Estado:</strong>
                            @if($user->is_active)
                                <span class="ml-2 rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700">Activo</span>
                            @else
                                <span class="ml-2 rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-700">Inactivo</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col gap-2 min-w-[190px]">
                        @can('users.edit')
                            @if($actorIsSuper || !$targetIsPrivileged)
                                <a href="{{ route('users.edit', $user) }}"
                                   class="text-center rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                                    Editar
                                </a>
                            @endif
                        @endcan

                        @can('users.deactivate')
                            @if($user->is_active && $user->id !== auth()->id() && !$targetIsSuper && ($actorIsSuper || !$targetIsPrivileged))
                                <form method="POST" action="{{ route('users.deactivate', $user) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="w-full rounded-xl border border-red-200 px-4 py-2 text-sm text-red-700"
                                            onclick="return confirm('¿Desactivar este usuario?')">
                                        Desactivar
                                    </button>
                                </form>
                            @endif

                            @if(!$user->is_active && !$targetIsSuper && ($actorIsSuper || !$targetIsPrivileged))
                                <form method="POST" action="{{ route('users.activate', $user) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="w-full rounded-xl border border-green-200 px-4 py-2 text-sm text-green-700"
                                            onclick="return confirm('¿Activar este usuario?')">
                                        Activar
                                    </button>
                                </form>
                            @endif
                        @endcan

                        @if($user->id === auth()->id())
                            <p class="text-xs text-gray-500 mt-1">
                                No puedes desactivar tu propia cuenta.
                            </p>
                        @endif
                    </div>
                </div>

                <div class="mt-6 border-t pt-6">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Proyectos en los que participa</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Proyectos donde este usuario forma parte del equipo.
                            </p>
                        </div>

                        <span class="rounded-full bg-violet-50 px-3 py-1 text-xs font-semibold text-violet-700">
                            {{ $projects->count() }} proyecto{{ $projects->count() === 1 ? '' : 's' }}
                        </span>
                    </div>

                    @if($projects->isEmpty())
                        <div class="mt-4 rounded-xl bg-gray-50 p-4 text-sm text-gray-500">
                            Este usuario no está asignado a ningún proyecto.
                        </div>
                    @else
                        <div class="mt-4 overflow-hidden rounded-xl border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                            Proyecto
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                            Estado
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                            Participación
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white">
                                    @foreach($projects as $project)
                                        <tr>
                                            <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                                {{ $project->name ?? ('Proyecto #' . $project->id) }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600">
                                                {{ $statusLabel($project->status ?? null) }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600">
                                                @if(($project->pivot->is_manager ?? false))
                                                    <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                                        Manager
                                                    </span>
                                                @else
                                                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                                        Miembro del equipo
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                @can('users.role.assign')
                    @if($user->id !== auth()->id() && ($actorIsSuper || !$targetIsPrivileged))
                        <div class="mt-6 border-t pt-4">
                            <div class="text-sm font-semibold mb-2">Cambiar rol</div>

                            <form method="POST" action="{{ route('users.role.assign', $user) }}" class="flex gap-2 items-end">
                                @csrf
                                @method('PATCH')

                                @php
                                    $currentRole = $user->getRoleNames()->first() ?? 'junior';
                                    $rolesAllowed = $actorIsSuper
                                        ? ['admin', 'senior', 'junior', 'intern']
                                        : ['senior', 'junior', 'intern'];
                                @endphp

                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Rol</label>
                                    <select name="role" class="mt-1 w-full rounded-xl border-gray-200 bg-white text-sm">
                                        @foreach($rolesAllowed as $r)
                                            <option value="{{ $r }}" @selected($currentRole === $r)>
                                                {{ ucfirst($r) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <button class="rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                                    Guardar
                                </button>
                            </form>
                        </div>
                    @endif
                @endcan

                @can('users.permissions.assign')
                    @php
                        $actor = auth()->user();
                        $actorIsSuper = $actor->hasRole('superadmin');
                        $actorCanManagePermissions = $actor->hasAnyRole(['admin', 'superadmin']);
                        $targetCanReceiveManagedPermissions = $user->hasAnyRole(['senior', 'junior', 'intern']);

                        $direct = $user->getDirectPermissions()->pluck('name')->toArray();
                        $inherited = $user->getPermissionsViaRoles()->pluck('name')->toArray();

                        $visiblePermissions = $permissionsForAssignment
                            ->filter(fn ($perm) => $actorIsSuper || !str_starts_with($perm->name, 'users.'))
                            ->reject(fn ($perm) => in_array($perm->name, $inherited, true))
                            ->values();

                        $editableDirectCount = count(array_intersect(
                            $direct,
                            $visiblePermissions->pluck('name')->toArray()
                        ));
                    @endphp

                    @if($actorCanManagePermissions && $targetCanReceiveManagedPermissions && $user->id !== auth()->id())
                        <details class="mt-6 border-t pt-4">
                            <summary class="flex cursor-pointer items-center justify-between rounded-xl border bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-900">
                                <span>Gestionar permisos</span>
                                <span class="text-xs text-gray-500">{{ $editableDirectCount }} permisos extra</span>
                            </summary>

                            <div class="mt-4 space-y-4">
                                <p class="text-sm text-gray-600">
                                    Aquí puedes añadir o quitar permisos directos extra. Los permisos heredados del rol no se editan aquí.
                                </p>

                                @if($visiblePermissions->isEmpty())
                                    <p class="text-sm text-gray-500">
                                        Este usuario ya tiene por rol todos los permisos visibles para su nivel, así que no hay permisos extra disponibles para gestionar.
                                    </p>
                                @else
                                    <form method="POST" action="{{ route('users.permissions.update', $user) }}" class="space-y-4">
                                        @csrf
                                        @method('PATCH')

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            @foreach($visiblePermissions as $perm)
                                                @php
                                                    $name = $perm->name;
                                                    $meta = $permissionCatalog[$name] ?? [
                                                        'label' => ucfirst(str_replace(['.', '_'], [' ', ' '], $name)),
                                                        'description' => 'Permiso adicional del sistema.',
                                                    ];
                                                    $isDirect = in_array($name, $direct, true);
                                                @endphp

                                                <label class="flex items-start gap-3 rounded-xl border p-3">
                                                    <input
                                                        type="checkbox"
                                                        name="permissions[]"
                                                        value="{{ $name }}"
                                                        class="mt-1 rounded"
                                                        @checked($isDirect)
                                                    >

                                                    <div class="flex-1">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $meta['label'] }}
                                                        </div>

                                                        @if(!empty($meta['description']))
                                                            <div class="text-xs text-gray-500 mt-1">
                                                                {{ $meta['description'] }}
                                                            </div>
                                                        @endif

                                                        <div class="text-xs text-gray-500 mt-1">
                                                            {{ $isDirect ? 'Permiso extra activo' : 'Sin permiso extra' }}
                                                        </div>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>

                                        <button class="rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                                            Guardar permisos
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </details>
                    @elseif($actorCanManagePermissions && $user->id !== auth()->id())
                        <details class="mt-6 border-t pt-4">
                            <summary class="flex cursor-pointer items-center justify-between rounded-xl border bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-900">
                                <span>Gestionar permisos</span>
                                <span class="text-xs text-gray-500">No disponible</span>
                            </summary>

                            <div class="mt-4">
                                <p class="text-sm text-gray-500">
                                    La gestión de permisos extra solo está disponible para usuarios senior, junior e intern.
                                </p>
                            </div>
                        </details>
                    @endif
                @endcan
            </div>
        </div>
    </div>
</x-app-layout>