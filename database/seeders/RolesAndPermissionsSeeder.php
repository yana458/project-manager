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
            'users.view',
            'users.create',
            'users.edit',
            'users.deactivate',
            'users.role.assign',
            'users.permissions.assign',

            // clients
            'clients.view',
            'clients.create',
            'clients.edit',
            'clients.deactivate',

            // services
            'services.view',
            'services.create',
            'services.edit',
            'services.deactivate',

            // client services
            'client_services.view',
            'client_services.manage',

            // projects
            'projects.view',
            'projects.create',
            'projects.edit',
            'projects.deactivate',
            'projects.status.change',

            // project services
            'project_services.view',
            'project_services.manage',
            'project_services.progress.update',

            // project team
            'project_team.view',
            'project_team.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                [
                    'name' => $permission,
                    'guard_name' => $guard,
                ],
                []
            );
        }

        $superadmin = Role::updateOrCreate(['name' => 'superadmin', 'guard_name' => $guard]);
        $admin      = Role::updateOrCreate(['name' => 'admin', 'guard_name' => $guard]);
        $senior     = Role::updateOrCreate(['name' => 'senior', 'guard_name' => $guard]);
        $junior     = Role::updateOrCreate(['name' => 'junior', 'guard_name' => $guard]);
        $intern     = Role::updateOrCreate(['name' => 'intern', 'guard_name' => $guard]);

        // superadmin: todo
        $superadmin->syncPermissions($permissions);

        // admin: todo
        $admin->syncPermissions($permissions);

        // senior
        $senior->syncPermissions([
            'clients.view',
            'clients.edit',

            'projects.view',
            'projects.edit',
            'projects.status.change',

            'project_services.view',
            'project_services.manage',

            'project_team.view',
            'project_team.manage',

            'client_services.view',

            'services.view',
        ]);

        // junior
        $junior->syncPermissions([
            'clients.view',

            'projects.view',

            'project_services.view',
            'project_services.progress.update',

            'project_team.view',

            'client_services.view',

            'services.view',
        ]);

        // intern
        $intern->syncPermissions([
            'clients.view',

            'projects.view',

            'project_services.view',

            'project_team.view',

            'client_services.view',

            'services.view',
        ]);

        /*
        |--------------------------------------------------------------------------
        | SUPERADMIN fijo desde .env
        |--------------------------------------------------------------------------
        */
        $superEmail = env('SUPERADMIN_EMAIL');
        $superPass  = env('SUPERADMIN_PASSWORD');

        if ($superEmail && $superPass) {
            $superUser = User::firstOrCreate(
                ['email' => $superEmail],
                [
                    'name' => 'Super Admin',
                    'password' => Hash::make($superPass),
                    'department' => 'development',
                    'is_active' => true,
                ]
            );

            $superUser->update([
                'department' => $superUser->department ?? 'development',
                'is_active' => $superUser->is_active ?? true,
            ]);

            $superUser->syncRoles(['superadmin']);
        }

        /*
        |--------------------------------------------------------------------------
        | ADMIN fijo desde .env
        |--------------------------------------------------------------------------
        */
        $adminEmail = env('ADMIN_EMAIL', 'admin@example.com');
        $adminPass  = env('ADMIN_PASSWORD', 'admin12345');

        $adminUser = User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'Admin',
                'password' => Hash::make($adminPass),
                'department' => 'marketing',
                'is_active' => true,
            ]
        );

        $adminUser->update([
            'department' => $adminUser->department ?? 'marketing',
            'is_active' => $adminUser->is_active ?? true,
        ]);

        $adminUser->syncRoles(['admin']);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}