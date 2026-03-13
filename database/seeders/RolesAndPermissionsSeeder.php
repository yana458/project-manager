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
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $guard = 'web';

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

        // Crear/asegurar permisos con guard correcto
        foreach ($permissions as $perm) {
            Permission::updateOrCreate(
                ['name' => $perm, 'guard_name' => $guard],
                []
            );
        }

        // Roles (incluye superadmin)
        $superadmin = Role::updateOrCreate(['name' => 'superadmin', 'guard_name' => $guard]);
        $admin      = Role::updateOrCreate(['name' => 'admin',      'guard_name' => $guard]);
        $senior     = Role::updateOrCreate(['name' => 'senior',     'guard_name' => $guard]);
        $junior     = Role::updateOrCreate(['name' => 'junior',     'guard_name' => $guard]);
        $intern     = Role::updateOrCreate(['name' => 'intern',     'guard_name' => $guard]);

        // Asignación de permisos
        // superadmin: todo (igual que admin)
        $superadmin->syncPermissions($permissions);

        // admin: todo
        $admin->syncPermissions($permissions);

        // senior
        $senior->syncPermissions([
            'clients.view','clients.edit',
            'projects.view','projects.edit','projects.status.change',
            'project_services.view','project_services.manage',
            'project_team.view','project_team.manage',
            'client_services.view',
            'services.view',
        ]);

        // junior
        $junior->syncPermissions([
            'clients.view',
            'projects.view',
            'project_services.view','project_services.progress.update',
            'project_team.view',
            'client_services.view',
        ]);

        // intern
        $intern->syncPermissions([
            'clients.view',
            'projects.view',
            'project_services.view',
            'project_team.view',
            'client_services.view',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Crear usuario SUPERADMIN fijo (solo por .env)
        |--------------------------------------------------------------------------
        */
        $superEmail = env('SUPERADMIN_EMAIL');
        $superPass  = env('SUPERADMIN_PASSWORD');

        if ($superEmail && $superPass) {
            $su = User::firstOrCreate(
                ['email' => $superEmail],
                [
                    'name' => 'Super Admin',
                    'password' => Hash::make($superPass),
                    'department' => 'development',
                    'is_active' => true,
                ]
            );

            // Asegurar rol y estado/department si ya existía
            $su->update([
                'department' => $su->department ?? 'development',
                'is_active' => $su->is_active ?? true,
            ]);

            $su->syncRoles(['superadmin']);
        }

        /*
        |--------------------------------------------------------------------------
        | Crear usuario ADMIN fijo (normal) por .env
        |--------------------------------------------------------------------------
        */
        $adminEmail = env('ADMIN_EMAIL', 'admin@example.com');
        $adminPass  = env('ADMIN_PASSWORD', 'admin12345');

        $au = User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'Admin',
                'password' => Hash::make($adminPass),
                'department' => 'marketing', // o el depto que quieras por defecto
                'is_active' => true,
            ]
        );

        // Asegurar valores mínimos si ya existía
        $au->update([
            'department' => $au->department ?? 'marketing',
            'is_active' => $au->is_active ?? true,
        ]);

        $au->syncRoles(['admin']);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}