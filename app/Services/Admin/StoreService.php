<?php

namespace App\Services\Admin;

use App\Models\Store;
use App\Enums\StoreStatus;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class StoreService
{
    /**
     * Get all stores with pagination and filters
     */
    public function getAllStores(array $filters): LengthAwarePaginator
    {
        $query = Store::with(['user']);

        // Apply search filter
        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('store_name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('domain', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('store_email', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Apply platform filter
        if (isset($filters['platform'])) {
            $query->where('platform', $filters['platform']);
        }

        // Apply status filter
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply user filter
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        // Paginate results
        $perPage = $filters['per_page'] ?? 15;
        return $query->paginate($perPage);
    }

    /**
     * Get store by ID
     */
    public function getStoreById(int $id): Store
    {
        return Store::with(['user', 'webhookLogs'])
            ->findOrFail($id);
    }

    /**
     * Update a store
     */
    public function updateStore(int $id, array $data): Store
    {
        return DB::transaction(function () use ($id, $data) {
            $store = Store::findOrFail($id);
            $store->update($data);

            return $store->fresh();
        });
    }

    /**
     * Delete a store
     */
    public function deleteStore(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $store = Store::findOrFail($id);
            return $store->delete();
        });
    }

    /**
     * Refresh store token
     */
    public function refreshStoreToken(int $id): Store
    {
        $store = Store::findOrFail($id);

        // Get appropriate auth service based on platform
        $authService = match ($store->platform->value) {
            'salla' => app(\App\Services\Store\Salla\SallaAuthService::class),
            'zid' => app(\App\Services\Store\Zid\ZidAuthService::class),
            'wordpress' => app(\App\Services\Store\WordPress\WordPressAuthService::class),
        };

        $authService->refreshToken($store);

        return $store->fresh();
    }
}
