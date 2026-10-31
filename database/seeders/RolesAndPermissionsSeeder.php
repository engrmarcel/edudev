<?php

namespace Database\Seeders;

use App\Enums\Permission as AppPermission;
use App\Enums\Role as AppRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Reference data: platform roles and permissions. Safe to run repeatedly.
 */
class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (AppPermission::cases() as $permission) {
            Permission::findOrCreate($permission->value, 'web');
        }

        foreach (AppRole::cases() as $role) {
            Role::findOrCreate($role->value, 'web');
        }

        foreach (AppPermission::matrix() as $roleName => $permissions) {
            Role::findByName($roleName, 'web')
                ->syncPermissions(array_map(fn (AppPermission $p) => $p->value, $permissions));
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
