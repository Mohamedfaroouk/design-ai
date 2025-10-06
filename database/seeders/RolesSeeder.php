<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles (guard_name is required by Spatie)
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'web'],
            ['is_system' => true]
        );
        $adminRole->update(['is_system' => true]); // Ensure it's set even if it already exists

        $clientRole = Role::firstOrCreate(
            ['name' => 'client', 'guard_name' => 'web'],
            ['is_system' => true]
        );
        $clientRole->update(['is_system' => true]); // Ensure it's set even if it already exists

        // Create permissions (you can expand this list)
        $permissions = [
            // User management
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            // Role management
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',

            // Dashboard
            'dashboard.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign all permissions to admin
        $adminRole->syncPermissions(Permission::all());

        // Client role has no permissions - they get basic access by default
        $clientRole->syncPermissions([]);
    }
}
