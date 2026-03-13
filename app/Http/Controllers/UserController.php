<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

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
        return view('users.show', compact('user'));
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

        // Default de asignar el rol 'junior' a los nuevos usuarios, a menos que ya tengan un rol asignado
        if (method_exists($user, 'assignRole') && !$user->hasAnyRole(['admin','senior','junior','intern'])) {
            $user->assignRole('junior');
        }

        return redirect()->route('users.index')->with('success', 'User created.');
    }

    public function edit(User $user)
    {
        $departments = User::DEPARTMENTS;
        $rolesAllowed = $this->assignableRoles();
        return view('users.edit', compact('user', 'departments', 'rolesAllowed'));
    }

    public function update(Request $request, User $user)
    {
        // No permitir cambiar tu propio rol (opcional, recomendado)
        $editingSelf = ($user->id === Auth::id());

        // Si el usuario objetivo es privilegiado (admin/superadmin), solo superadmin puede editar su rol/estado
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

        // No cambiar el rol ni permitir desactivar al propio usuario
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

        // Cambia rol si venía y NO es el propio usuario
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
        // No permitir cambiar el rol al propio usuario
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot change your own role.');
        }

        // Si el usuario objetivo es admin/superadmin, solo superadmin puede tocarlo
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
}