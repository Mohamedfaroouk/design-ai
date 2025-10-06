<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Http\Resources\Admin\RoleResource;
use App\Services\Admin\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    protected RoleService $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Display a listing of roles
     */
    public function index(Request $request): JsonResponse
    {
        $roles = $this->roleService->getAllRoles($request->all());

        return response()->json([
            'data' => RoleResource::collection($roles->items()),
            'meta' => [
                'current_page' => $roles->currentPage(),
                'last_page' => $roles->lastPage(),
                'per_page' => $roles->perPage(),
                'total' => $roles->total(),
                'from' => $roles->firstItem(),
                'to' => $roles->lastItem(),
            ]
        ]);
    }

    /**
     * Store a newly created role
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        try {
            $role = $this->roleService->createRole($request->validated());

            return response()->json([
                'success' => true,
                'message' => __('roles.created'),
                'data' => new RoleResource($role)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified role
     */
    public function show(int $id): JsonResponse
    {
        try {
            $role = $this->roleService->getRoleById($id);

            return response()->json([
                'success' => true,
                'data' => new RoleResource($role)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified role
     */
    public function update(UpdateRoleRequest $request, int $id): JsonResponse
    {
        try {
            $role = $this->roleService->updateRole($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => __('roles.updated'),
                'data' => new RoleResource($role)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Remove the specified role
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->roleService->deleteRole($id);

            return response()->json([
                'success' => true,
                'message' => __('roles.deleted')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get all available permissions
     */
    public function permissions(): JsonResponse
    {
        try {
            $permissions = $this->roleService->getAllPermissions();

            return response()->json([
                'success' => true,
                'data' => $permissions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
