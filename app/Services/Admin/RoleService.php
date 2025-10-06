<?php

namespace App\Services\Admin;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleService
{
    /**
     * Get all roles with permissions
     */
    public function getAllRoles(array $params = [])
    {
        $query = Role::with('permissions');

        // Search
        if (!empty($params['search'])) {
            $search = $params['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $params['sortBy'] ?? 'created_at';
        $sortOrder = $params['sortOrder'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        // Paginate
        $perPage = $params['perPage'] ?? 15;
        return $query->paginate($perPage);
    }

    /**
     * Get role by ID
     */
    public function getRoleById(int $id): Role
    {
        return Role::with('permissions')->findOrFail($id);
    }

    /**
     * Create new role
     */
    public function createRole(array $data): Role
    {
        return DB::transaction(function () use ($data) {
            // Create role (user-created roles are never system roles)
            $role = Role::create([
                'name' => $data['name'],
                'guard_name' => 'web',
                'is_system' => false,
            ]);

            // Assign permissions
            if (!empty($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }

            return $role->load('permissions');
        });
    }

    /**
     * Update role
     */
    public function updateRole(int $id, array $data): Role
    {
        return DB::transaction(function () use ($id, $data) {
            $role = Role::findOrFail($id);

            // Prevent modification of system roles
            if ($role->is_system) {
                throw new \Exception(__('roles.cannot_modify_system_role'));
            }

            // Update role name
            $role->update([
                'name' => $data['name'],
            ]);

            // Update permissions
            if (isset($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }

            return $role->load('permissions');
        });
    }

    /**
     * Delete role
     */
    public function deleteRole(int $id): bool
    {
        $role = Role::findOrFail($id);

        // Prevent deleting system roles
        if ($role->is_system) {
            throw new \Exception(__('roles.cannot_delete_system_role'));
        }

        // Check if role is assigned to any users
        if ($role->users()->count() > 0) {
            throw new \Exception(__('roles.cannot_delete_assigned_role'));
        }

        return $role->delete();
    }

    /**
     * Get all permissions
     */
    public function getAllPermissions(): array
    {
        return Permission::orderBy('name')->get()->toArray();
    }
}
