<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    public function index()
    {
        $users = User::query()
            ->orderBy('name')
            ->paginate(10);

        return view('users.index', compact('users'));
    }

    public function show(User $user)
    {
        if (method_exists($user, 'projects')) {
            $user->load([
                'projects' => function ($query) {
                    $query->orderByDesc('projects.id');
                },
            ]);
        }

        $permissionsForAssignment = Permission::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get();

        $permissionCatalog = $this->permissionCatalog();

        return view('users.show', compact('user', 'permissionsForAssignment', 'permissionCatalog'));
    }

    public function create()
    {
        $departments = User::DEPARTMENTS;
        $roles = ['admin', 'senior', 'junior', 'intern'];

        return view('users.create', compact('departments', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'department' => ['required', Rule::in(User::DEPARTMENTS)],
            'role' => ['required', Rule::in($this->assignableRoles())],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'department' => $validated['department'],
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        $user->syncRoles([$validated['role']]);

        if (method_exists($user, 'assignRole') && !$user->hasAnyRole(['admin', 'senior', 'junior', 'intern'])) {
            $user->assignRole('junior');
        }

        return redirect()->route('users.index')->with('success', 'User created.');
    }

    public function edit(User $user)
    {
        if ($user->hasRole('superadmin') && !Auth::user()?->hasRole('superadmin')) {
            return redirect()
                ->route('users.show', $user)
                ->with('error', 'Only superadmin can edit a superadmin.');
        }

        if (method_exists($user, 'projects')) {
            $user->load([
                'projects' => function ($query) {
                    $query->orderByDesc('projects.id');
                },
            ]);
        }

        $departments = User::DEPARTMENTS;
        $rolesAllowed = $this->assignableRoles();

        return view('users.edit', compact('user', 'departments', 'rolesAllowed'));
    }

    public function update(Request $request, User $user)
    {
        $editingSelf = ($user->id === Auth::id());

        if ($this->isPrivileged($user) && !Auth::user()?->hasRole('superadmin')) {
            return back()->with('error', 'Only superadmin can manage admin accounts.');
        }

        $rolesAllowed = $this->assignableRoles();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'department' => ['required', Rule::in(User::DEPARTMENTS)],
            'role' => [$editingSelf ? 'nullable' : 'required', Rule::in($rolesAllowed)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if ($user->id === Auth::id()) {
            unset($validated['role']);
        }

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'department' => $validated['department'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        if (isset($validated['role'])) {
            $user->syncRoles([$validated['role']]);
        }

        return redirect()->route('users.show', $user)->with('success', 'User updated.');
    }

    private function assignableRoles(): array
    {
        if (Auth::user() && Auth::user()->hasRole('superadmin')) {
            return ['admin', 'senior', 'junior', 'intern'];
        }

        return ['senior', 'junior', 'intern'];
    }

    private function isPrivileged(User $user): bool
    {
        return $user->hasAnyRole(['superadmin', 'admin']);
    }

    public function activate(User $user)
    {
        if ($this->isPrivileged($user) && !Auth::user()->hasRole('superadmin')) {
            return back()->with('error', 'Only superadmin can activate admin accounts.');
        }

        if ($user->is_active) {
            return back()->with('success', 'User is already active.');
        }

        $user->update(['is_active' => true]);

        return back()->with('success', 'User activated.');
    }

    public function assignRole(Request $request, User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot change your own role.');
        }

        if ($this->isPrivileged($user) && !Auth::user()->hasRole('superadmin')) {
            return back()->with('error', 'Only superadmin can manage admin accounts.');
        }

        $validated = $request->validate([
            'role' => ['required', Rule::in($this->assignableRoles())],
        ]);

        $user->syncRoles([$validated['role']]);

        return back()->with('success', 'Role updated.');
    }

    public function deactivate(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        if ($this->isPrivileged($user) && !Auth::user()->hasRole('superadmin')) {
            return back()->with('error', 'Only superadmin can deactivate admin accounts.');
        }

        $user->update(['is_active' => false]);

        return back()->with('success', 'User deactivated.');
    }

    public function updatePermissions(Request $request, User $user)
    {
        /** @var \App\Models\User|null $actor */
        $actor = Auth::user();

        if (!$actor || !$actor->hasAnyRole(['admin', 'superadmin'])) {
            abort(403);
        }

        if ($user->id === $actor->id) {
            return back()->with('error', 'No puedes modificar tus propios permisos.');
        }

        if (!$user->hasAnyRole(['senior', 'junior', 'intern'])) {
            return back()->with('error', 'Solo se pueden gestionar permisos extra para senior, junior e intern.');
        }

        $allVisible = Permission::query()
            ->where('guard_name', 'web')
            ->pluck('name')
            ->filter(fn ($name) => $actor->hasRole('superadmin') || !str_starts_with($name, 'users.'))
            ->values();

        $inherited = $user->getPermissionsViaRoles()->pluck('name')->toArray();

        $editable = $allVisible
            ->reject(fn ($name) => in_array($name, $inherited, true))
            ->values()
            ->all();

        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in($editable)],
        ]);

        $currentDirect = $user->getDirectPermissions()->pluck('name');

        $hiddenDirect = $currentDirect
            ->reject(fn ($name) => in_array($name, $editable, true))
            ->values()
            ->all();

        $selectedEditable = $validated['permissions'] ?? [];

        $user->syncPermissions(array_values(array_unique([
            ...$hiddenDirect,
            ...$selectedEditable,
        ])));

        return back()->with('success', 'Permisos extra actualizados correctamente.');
    }

    private function permissionCatalog(): array
    {
        return [
            'users.view' => [
                'label' => 'Ver usuarios',
                'description' => 'Permite consultar el listado y la ficha de usuarios.',
            ],
            'users.create' => [
                'label' => 'Crear usuarios',
                'description' => 'Permite dar de alta nuevos usuarios.',
            ],
            'users.edit' => [
                'label' => 'Editar usuarios',
                'description' => 'Permite modificar los datos de un usuario.',
            ],
            'users.deactivate' => [
                'label' => 'Desactivar usuarios',
                'description' => 'Permite activar o desactivar cuentas.',
            ],
            'users.role.assign' => [
                'label' => 'Asignar rol',
                'description' => 'Permite cambiar el rol global del usuario.',
            ],
            'users.permissions.assign' => [
                'label' => 'Gestionar permisos extra',
                'description' => 'Permite asignar permisos directos adicionales.',
            ],

            'clients.view' => [
                'label' => 'Ver clientes',
                'description' => 'Permite consultar clientes y su información.',
            ],
            'clients.create' => [
                'label' => 'Crear clientes',
                'description' => 'Permite registrar nuevos clientes.',
            ],
            'clients.edit' => [
                'label' => 'Editar clientes',
                'description' => 'Permite modificar datos de clientes.',
            ],
            'clients.deactivate' => [
                'label' => 'Desactivar clientes',
                'description' => 'Permite activar o desactivar clientes.',
            ],

            'services.view' => [
                'label' => 'Ver catálogo de servicios',
                'description' => 'Permite consultar los servicios disponibles.',
            ],
            'services.create' => [
                'label' => 'Crear servicios',
                'description' => 'Permite añadir servicios al catálogo.',
            ],
            'services.edit' => [
                'label' => 'Editar servicios',
                'description' => 'Permite modificar servicios del catálogo.',
            ],
            'services.deactivate' => [
                'label' => 'Desactivar servicios',
                'description' => 'Permite activar o desactivar servicios.',
            ],

            'client_services.view' => [
                'label' => 'Ver servicios contratados',
                'description' => 'Permite consultar los servicios contratados por cliente.',
            ],
            'client_services.manage' => [
                'label' => 'Gestionar servicios contratados',
                'description' => 'Permite asignar o retirar servicios a un cliente.',
            ],

            'projects.view' => [
                'label' => 'Ver proyectos',
                'description' => 'Permite consultar el listado y detalle de proyectos.',
            ],
            'projects.create' => [
                'label' => 'Crear proyectos',
                'description' => 'Permite crear nuevos proyectos.',
            ],
            'projects.edit' => [
                'label' => 'Editar proyectos',
                'description' => 'Permite modificar datos de proyectos.',
            ],
            'projects.deactivate' => [
                'label' => 'Desactivar proyectos',
                'description' => 'Permite activar o desactivar proyectos.',
            ],
            'projects.status.change' => [
                'label' => 'Cambiar estado del proyecto',
                'description' => 'Permite pausar, reanudar o finalizar proyectos.',
            ],

            'project_services.view' => [
                'label' => 'Ver servicios del proyecto',
                'description' => 'Permite consultar los servicios vinculados a un proyecto.',
            ],
            'project_services.manage' => [
                'label' => 'Gestionar servicios del proyecto',
                'description' => 'Permite añadir o quitar servicios dentro de un proyecto.',
            ],
            'project_services.progress.update' => [
                'label' => 'Actualizar progreso de servicios',
                'description' => 'Permite registrar el avance de los servicios del proyecto.',
            ],

            'project_team.view' => [
                'label' => 'Ver equipo del proyecto',
                'description' => 'Permite consultar qué usuarios participan en cada proyecto.',
            ],
            'project_team.manage' => [
                'label' => 'Gestionar equipo del proyecto',
                'description' => 'Permite asignar o quitar miembros del equipo.',
            ],
        ];
    }
}