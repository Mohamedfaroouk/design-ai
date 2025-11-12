<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Http\Resources\Admin\RoleResource;
use App\Services\Admin\RoleService;
use App\Traits\HasDataTableInertia;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class WebRoleController extends Controller
{
    use HasDataTableInertia;

    protected RoleService $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Display a listing of roles
     */
    public function index(Request $request): Response
    {
        return $this->inertiaDataTable(
            page: 'Modules/admin/Roles/RolesIndex',
            query: Role::with(['permissions']),
            request: $request,
            resource: RoleResource::class,
            searchable: ['name'],
            filterable: [],
            defaultSort: 'created_at',
            defaultOrder: 'desc',
            additionalProps: []
        );
    }

    /**
     * Show the form for creating a new role
     */
    public function create(): Response
    {
        $permissions = $this->roleService->getAllPermissions();

        return Inertia::render('Modules/admin/Roles/RolesForm', [
            'permissions' => $permissions,
        ]);
    }

    /**
     * Store a newly created role
     */
    public function store(StoreRoleRequest $request): RedirectResponse
    {
        try {
            $this->roleService->createRole($request->validated());

            return redirect()->route('admin.roles.index')
                ->with('success', __('roles.created'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified role
     */
    public function edit(int $id): Response
    {
        try {
            $role = $this->roleService->getRoleById($id);
            $permissions = $this->roleService->getAllPermissions();

            return Inertia::render('Modules/admin/Roles/RolesForm', [
                'role' => new RoleResource($role),
                'permissions' => $permissions,
            ]);
        } catch (\Exception $e) {
            abort(404, $e->getMessage());
        }
    }

    /**
     * Update the specified role
     */
    public function update(UpdateRoleRequest $request, int $id): RedirectResponse
    {
        try {
            $this->roleService->updateRole($id, $request->validated());

            return redirect()->route('admin.roles.index')
                ->with('success', __('roles.updated'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified role
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->roleService->deleteRole($id);

            return redirect()->back()
                ->with('success', __('roles.deleted'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
}
