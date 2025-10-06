<?php

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserService
{
    /**
     * Get all users except those who only have client role
     */
    public function getAllUsers(array $params = [])
    {
        $query = User::with('roles')
            ->whereHas('roles', function ($q) {
                // Get users who have roles other than 'client' OR have no roles
                $q->where('name', '!=', 'client');
            });

        // Search
        if (!empty($params['search'])) {
            $search = $params['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if (!empty($params['role'])) {
            $query->whereHas('roles', function ($q) use ($params) {
                $q->where('name', $params['role']);
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
     * Get user by ID
     */
    public function getUserById(int $id): User
    {
        return User::with('roles', 'permissions')->findOrFail($id);
    }

    /**
     * Create new user
     */
    public function createUser(array $data): User
    {
        return DB::transaction(function () use ($data) {
            // Create user
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'email_verified_at' => now(), // Auto-verify for admin created users
            ]);

            // Assign role
            if (!empty($data['role'])) {
                $user->assignRole($data['role']);
            }

            return $user->load('roles');
        });
    }

    /**
     * Update user
     */
    public function updateUser(int $id, array $data): User
    {
        return DB::transaction(function () use ($id, $data) {
            $user = User::findOrFail($id);

            // Update basic info
            $user->update([
                'name' => $data['name'],
                'email' => $data['email'],
            ]);

            // Update password if provided
            if (!empty($data['password'])) {
                $user->update([
                    'password' => Hash::make($data['password'])
                ]);
            }

            // Update role
            if (isset($data['role'])) {
                $user->syncRoles([$data['role']]);
            }

            return $user->load('roles');
        });
    }

    /**
     * Delete user
     */
    public function deleteUser(int $id): bool
    {
        $user = User::findOrFail($id);

        // Prevent deleting yourself (assuming authenticated user)
        if (auth()->id() === $user->id) {
            throw new \Exception(__('users.cannot_delete_yourself'));
        }

        return $user->delete();
    }
}
