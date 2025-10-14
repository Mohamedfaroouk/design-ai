<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePackageRequest;
use App\Http\Requests\Admin\UpdatePackageRequest;
use App\Http\Resources\Admin\PackageResource;
use App\Models\Package;
use App\Traits\HasDataTable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    use HasDataTable;

    /**
     * Display a listing of packages
     */
    public function index(Request $request): JsonResponse
    {
        $query = Package::query();

        return $this->dataTableResponse(
            query: $query,
            request: $request,
            resource: PackageResource::class,
            searchable: ['name', 'display_name', 'description'],
            filterable: ['platform', 'billing_cycle', 'is_active', 'is_featured'],
            defaultSort: 'sort_order',
            defaultOrder: 'asc',
            defaultPerPage: 15
        );
    }

    /**
     * Get packages list for dropdown (no pagination)
     */
    public function list(Request $request): JsonResponse
    {
        $query = Package::where('is_active', true)
                       ->orderBy('sort_order');

        // Filter by platform if provided
        if ($request->has('platform')) {
            $query->forPlatform($request->platform);
        }

        return $this->listResponse(
            query: $query,
            request: $request,
            resource: PackageResource::class,
            searchable: ['name', 'display_name'],
            limit: 100
        );
    }

    /**
     * Store a newly created package
     */
    public function store(StorePackageRequest $request): JsonResponse
    {
        $package = Package::create($request->validated());

        return response()->json([
            'message' => __('packages.created'),
            'data' => new PackageResource($package)
        ], 201);
    }

    /**
     * Display the specified package
     */
    public function show(int $id): JsonResponse
    {
        $package = Package::findOrFail($id);

        return response()->json([
            'data' => new PackageResource($package)
        ]);
    }

    /**
     * Update the specified package
     */
    public function update(UpdatePackageRequest $request, int $id): JsonResponse
    {
        $package = Package::findOrFail($id);
        $package->update($request->validated());

        return response()->json([
            'message' => __('packages.updated'),
            'data' => new PackageResource($package)
        ]);
    }

    /**
     * Remove the specified package
     */
    public function destroy(int $id): JsonResponse
    {
        $package = Package::findOrFail($id);

        // Check if package has active subscriptions
        if ($package->subscriptions()->active()->exists()) {
            return response()->json([
                'message' => __('packages.has_active_subscriptions')
            ], 422);
        }

        $package->delete();

        return response()->json([
            'message' => __('packages.deleted')
        ]);
    }
}
