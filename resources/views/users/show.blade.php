<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">User detail</h2>
            <a href="{{ route('users.index') }}" class="text-center rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
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
                @php
                    $actorIsSuper = auth()->user()->hasRole('superadmin');
                    $targetIsSuper = $user->hasRole('superadmin');
                    $targetIsPrivileged = $user->hasAnyRole(['superadmin', 'admin']);
                @endphp

                <div class="flex items-start justify-between gap-4">
                    <div class="space-y-2">
                        <div><strong>Name:</strong> {{ $user->name }}</div>
                        <div><strong>Email:</strong> {{ $user->email }}</div>
                        <div><strong>Department:</strong> {{ $user->department }}</div>
                        <div><strong>Role:</strong> {{ $user->getRoleNames()->implode(', ') ?: '-' }}</div>

                        <div class="pt-2">
                            <strong>Status:</strong>
                            @if($user->is_active)
                                <span class="ml-2 rounded-full bg-green-100 px-2 py-1 text-xs">active</span>
                            @else
                                <span class="ml-2 rounded-full bg-gray-100 px-2 py-1 text-xs">inactive</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col gap-2 min-w-[180px]">
                        @can('users.edit')
                            @if($actorIsSuper || !$targetIsPrivileged)
                                <a href="{{ route('users.edit', $user) }}"
                                   class="text-center rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                                    Edit
                                </a>
                            @endif
                        @endcan

                        @can('users.deactivate')
                            @if($user->is_active && $user->id !== auth()->id() && !$targetIsSuper && ($actorIsSuper || !$targetIsPrivileged))
                                <form method="POST" action="{{ route('users.deactivate', $user) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="w-full rounded-xl border border-red-200 px-4 py-2 text-sm text-red-700"
                                            onclick="return confirm('Deactivate this user?')">
                                        Deactivate
                                    </button>
                                </form>
                            @endif

                            @if(!$user->is_active && !$targetIsSuper && ($actorIsSuper || !$targetIsPrivileged))
                                <form method="POST" action="{{ route('users.activate', $user) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="w-full rounded-xl border border-green-200 px-4 py-2 text-sm text-green-700"
                                            onclick="return confirm('Activate this user?')">
                                        Activate
                                    </button>
                                </form>
                            @endif
                        @endcan

                        @if($user->id === auth()->id())
                            <p class="text-xs text-gray-500 mt-1">
                                You cannot deactivate your own account.
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Bloque de cambiar rol --}}
                @can('users.role.assign')
                    @if($user->id !== auth()->id() && ($actorIsSuper || !$targetIsPrivileged))
                        <div class="mt-6 border-t pt-4">
                            <div class="text-sm font-semibold mb-2">Change role</div>

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
                                    <label class="block text-sm font-medium text-gray-700">Role</label>
                                    <select name="role" class="mt-1 w-full rounded-xl border-gray-200 bg-white text-sm">
                                        @foreach($rolesAllowed as $r)
                                            <option value="{{ $r }}" @selected($currentRole === $r)>
                                                {{ ucfirst($r) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <button class="rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                                    Save
                                </button>
                            </form>
                        </div>
                    @endif
                @endcan

                {{-- Manage permissions --}}
@can('users.permissions.assign')
    @php
        $actor = auth()->user();
        $actorIsSuper = $actor->hasRole('superadmin');
        $actorCanManagePermissions = $actor->hasAnyRole(['admin', 'superadmin']);
        $targetCanReceiveManagedPermissions = $user->hasAnyRole(['senior', 'junior', 'intern']);

        $direct = $user->getDirectPermissions()->pluck('name')->toArray();
        $inherited = $user->getPermissionsViaRoles()->pluck('name')->toArray();

        // Solo permisos visibles para este actor y que NO vengan heredados del rol
        $visiblePermissions = $permissionsForAssignment
            ->filter(fn ($perm) => $actorIsSuper || !str_starts_with($perm->name, 'users.'))
            ->reject(fn ($perm) => in_array($perm->name, $inherited, true))
            ->values();

        $editableDirectCount = count(array_intersect(
            $direct,
            $visiblePermissions->pluck('name')->toArray()
        ));

        $permissionLabel = function (string $permission): string {
            $special = [
                'users.role.assign' => 'Asignar rol de usuario',
                'users.permissions.assign' => 'Asignar permisos de usuario',
                'projects.status.change' => 'Cambiar estado del proyecto',
                'project_services.progress.update' => 'Actualizar progreso del servicio del proyecto',
            ];

            if (isset($special[$permission])) {
                return $special[$permission];
            }

            $parts = explode('.', $permission);

            $moduleMap = [
                'users' => 'usuarios',
                'clients' => 'clientes',
                'services' => 'servicios',
                'client_services' => 'servicios del cliente',
                'projects' => 'proyectos',
                'project_services' => 'servicios del proyecto',
                'project_team' => 'equipo del proyecto',
            ];

            $actionMap = [
                'view' => 'Ver',
                'create' => 'Crear',
                'edit' => 'Editar',
                'deactivate' => 'Desactivar',
                'manage' => 'Gestionar',
                'assign' => 'Asignar',
                'change' => 'Cambiar',
                'update' => 'Actualizar',
            ];

            if (count($parts) === 2) {
                [$module, $action] = $parts;

                $moduleLabel = $moduleMap[$module] ?? str_replace('_', ' ', $module);
                $actionLabel = $actionMap[$action] ?? ucfirst(str_replace('_', ' ', $action));

                return $actionLabel . ' ' . $moduleLabel;
            }

            return ucfirst(str_replace(['.', '_'], [' ', ' '], $permission));
        };
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
                                    $label = $permissionLabel($name);
                                    $isDirect = in_array($name, $direct, true);
                                @endphp

                                <label class="flex items-center gap-3 rounded-xl border p-3">
                                    <input
                                        type="checkbox"
                                        name="permissions[]"
                                        value="{{ $name }}"
                                        class="rounded"
                                        @checked($isDirect)
                                    >

                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $label }}
                                        </div>

                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $name }}
                                        </div>

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