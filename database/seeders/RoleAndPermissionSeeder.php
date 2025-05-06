<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        // User management permissions
        Permission::create(['name' => 'create users']);
        Permission::create(['name' => 'edit users']);
        Permission::create(['name' => 'delete users']);
        Permission::create(['name' => 'view users']);

        // Role management permissions
        Permission::create(['name' => 'create roles']);
        Permission::create(['name' => 'edit roles']);
        Permission::create(['name' => 'delete roles']);
        Permission::create(['name' => 'view roles']);

        // Permission management
        Permission::create(['name' => 'assign permissions']);

        // IOT related permissions
        Permission::create(['name' => 'control devices']);
        Permission::create(['name' => 'view devices']);

        // Create roles and assign permissions
        $superAdminRole = Role::create(['name' => 'super-admin']);
        // Super admin gets all permissions
        $superAdminRole->givePermissionTo(Permission::all());

        // Admin role with limited permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo([
            'view users',
            'control devices',
            'view devices',
        ]);

        // Guest role with minimal permissions
        $guestRole = Role::create(['name' => 'guest']);
        $guestRole->givePermissionTo([
            'view devices',
        ]);
    }
}
