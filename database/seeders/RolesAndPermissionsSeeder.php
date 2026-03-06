<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Limpia cache de permisos antes de tocar roles/permisos
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $guard = 'web';

        // Lista de permisos
        $permissions = [
            // users
            'users.view','users.create','users.edit','users.deactivate','users.role.assign',

            // clients
            'clients.view','clients.create','clients.edit','clients.deactivate',

            // services (catálogo)
            'services.view','services.create','services.edit','services.deactivate',

            // client_services
            'client_services.view','client_services.manage',

            // projects
            'projects.view','projects.create','projects.edit','projects.deactivate','projects.status.change',

            // project_services
            'project_services.view','project_services.manage','project_services.progress.update',

            // project_team
            'project_team.view','project_team.manage',
        ];

        foreach ($permissions as $perm) {
            Permission::updateOrCreate(
                ['name' => $perm, 'guard_name' => $guard],
                []
            );
        }

        // Roles
    $admin  = Role::updateOrCreate(['name' => 'admin',  'guard_name' => $guard]);
    $senior = Role::updateOrCreate(['name' => 'senior', 'guard_name' => $guard]);
    $junior = Role::updateOrCreate(['name' => 'junior', 'guard_name' => $guard]);
    $intern = Role::updateOrCreate(['name' => 'intern', 'guard_name' => $guard]);

        // Asignación por rol 
        // admin: todo
        $admin->syncPermissions($permissions);

        // senior: ver + editar clientes/proyectos, sin "deactivate", y puede gestionar services/team del proyecto
        $senior->syncPermissions([
            'clients.view','clients.edit',
            'projects.view','projects.edit','projects.status.change',

            'project_services.view','project_services.manage',
            'project_team.view','project_team.manage',

            'client_services.view',
            'services.view',
        ]);

        // junior: consulta + actualizar progreso (sin editar proyecto "importante")
        $junior->syncPermissions([
            'clients.view',
            'projects.view',
            'project_services.view','project_services.progress.update',
            'project_team.view',
            'client_services.view',
        ]);

        // intern: solo consulta
        $intern->syncPermissions([
            'clients.view',
            'projects.view',
            'project_services.view',
            'project_team.view',
            'client_services.view',
        ]);

        // Usuario admin inicial (ajustar email/pass en .env)
        $adminEmail = env('ADMIN_EMAIL', 'admin@example.com');
        $adminPass  = env('ADMIN_PASSWORD', 'admin12345');

        $user = User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'Admin',
                'password' => Hash::make($adminPass),
            ]
        );

        if (! $user->hasRole('admin')) {
            $user->assignRole('admin');
        }

        // Opcional: reset cache (por si acaso)
        // (también se puede hacer con php artisan permission:cache-reset)
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}