<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar usuario</h2>
            <a href="{{ route('users.show', $user) }}" class="text-center rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                Volver
            </a>
        </div>
    </x-slot>

    @php
        $userProjects = $user->projects ?? collect();

        $statusLabels = [
            'active' => 'Activo',
            'paused' => 'Pausado',
            'finished' => 'Finalizado',
        ];

        $projectRoleLabels = [
            'manager' => 'Manager',
            'editor' => 'Editor',
            'viewer' => 'Viewer',
        ];
    @endphp

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-xl p-6">
                <div class="mb-6 rounded-xl bg-gray-50 p-4 text-sm text-gray-600">
                    Modifica los datos del usuario, su departamento, su rol global y, si lo necesitas, su contraseña.
                </div>

                <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre</label>
                        <input
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            class="mt-1 w-full rounded-xl border-gray-200 bg-white"
                            required
                        >
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Correo electrónico</label>
                        <input
                            name="email"
                            type="email"
                            value="{{ old('email', $user->email) }}"
                            class="mt-1 w-full rounded-xl border-gray-200 bg-white"
                            required
                        >
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Departamento</label>
                        <select
                            name="department"
                            class="mt-1 w-full rounded-xl border-gray-200 bg-white"
                            required
                        >
                            @foreach($departments as $dep)
                                <option value="{{ $dep }}" @selected(old('department', $user->department) === $dep)>
                                    {{ ucfirst($dep) }}
                                </option>
                            @endforeach
                        </select>
                        @error('department')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    @php
                        $currentRole = $user->getRoleNames()->first() ?? 'junior';
                    @endphp

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Rol</label>

                        <select
                            name="role"
                            class="mt-1 w-full rounded-xl border-gray-200 bg-white"
                            @disabled($user->id === auth()->id())
                        >
                            @foreach($rolesAllowed as $r)
                                <option value="{{ $r }}" @selected(old('role', $currentRole) === $r)>
                                    {{ ucfirst($r) }}
                                </option>
                            @endforeach
                        </select>

                        @if($user->id === auth()->id())
                            <p class="mt-1 text-xs text-gray-500">No puedes cambiar tu propio rol.</p>
                        @endif

                        @error('role')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-2 border-t"></div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nueva contraseña (opcional)</label>
                        <input
                            name="password"
                            type="password"
                            class="mt-1 w-full rounded-xl border-gray-200 bg-white"
                        >
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            Déjalo vacío si quieres mantener la contraseña actual.
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Confirmar nueva contraseña</label>
                        <input
                            name="password_confirmation"
                            type="password"
                            class="mt-1 w-full rounded-xl border-gray-200 bg-white"
                        >
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button class="rounded-xl bg-gray-900 px-4 py-2 text-sm text-white hover:bg-black">
                            Guardar cambios
                        </button>

                        <a href="{{ route('users.show', $user) }}" class="rounded-xl border px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>

            <div class="mt-6 bg-white shadow rounded-xl p-6">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Proyectos asignados</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Vista rápida de los proyectos en los que participa este usuario.
                        </p>
                    </div>

                    <span class="rounded-full bg-violet-50 px-3 py-1 text-xs font-semibold text-violet-700">
                        {{ $userProjects->count() }} proyecto{{ $userProjects->count() === 1 ? '' : 's' }}
                    </span>
                </div>

                @if($userProjects->isEmpty())
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
                                @foreach($userProjects as $project)
                                    @php
                                        $statusKey = $project->status ?? null;
                                        $statusText = $statusLabels[$statusKey] ?? ucfirst(str_replace('_', ' ', $statusKey ?? 'sin estado'));

                                        $roleKey = $project->pivot->project_role ?? null;
                                        $roleText = $projectRoleLabels[$roleKey] ?? 'Miembro del equipo';
                                    @endphp

                                    <tr>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                            {{ $project->name ?? ('Proyecto #' . $project->id) }}
                                        </td>

                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            {{ $statusText }}
                                        </td>

                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            @if($roleKey === 'manager')
                                                <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                                    Manager
                                                </span>
                                            @else
                                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                                    {{ $roleText }}
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
        </div>
    </div>
</x-app-layout>