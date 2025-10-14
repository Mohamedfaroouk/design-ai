<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateStoreRequest;
use App\Http\Resources\Admin\StoreResource;
use App\Services\Admin\StoreService;
use App\Traits\HasDataTable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    use HasDataTable;

    public function __construct(
        protected StoreService $storeService
    ) {
    }

    /**
     * Display a listing of stores
     */
    public function index(Request $request): JsonResponse
    {
        $query = \App\Models\Store::with(['user']);

        return $this->dataTableResponse(
            query: $query,
            request: $request,
            resource: StoreResource::class,
            searchable: ['store_name', 'domain', 'store_email', 'merchant_id'],
            filterable: ['platform', 'status', 'user_id'],
            defaultSort: 'created_at',
            defaultOrder: 'desc',
            defaultPerPage: 15
        );
    }

    /**
     * Display the specified store
     */
    public function show(int $id): JsonResponse
    {
        $store = $this->storeService->getStoreById($id);

        return response()->json([
            'data' => new StoreResource($store)
        ]);
    }

    /**
     * Update the specified store
     */
    public function update(UpdateStoreRequest $request, int $id): JsonResponse
    {
        $store = $this->storeService->updateStore($id, $request->validated());

        return response()->json([
            'message' => __('stores.update_success'),
            'data' => new StoreResource($store)
        ]);
    }

    /**
     * Remove the specified store
     */
    public function destroy(int $id): JsonResponse
    {
        $this->storeService->deleteStore($id);

        return response()->json([
            'message' => __('stores.delete_success')
        ]);
    }

    /**
     * Refresh store access token
     */
    public function refreshToken(int $id): JsonResponse
    {
        try {
            $store = $this->storeService->refreshStoreToken($id);

            return response()->json([
                'message' => __('stores.token_refresh_success'),
                'data' => new StoreResource($store)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => __('stores.token_refresh_failed'),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
