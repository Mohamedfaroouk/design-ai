<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Resources\Admin\UserResource;
use App\Services\Admin\UserService;
use App\Traits\HasDataTableInertia;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class WebUserController extends Controller
{
    use HasDataTableInertia;

    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of users
     */
    public function index(Request $request): Response
    {
        return $this->inertiaDataTable(
            page: 'Modules/admin/Users/UsersIndex',
            query: User::with(['roles']),
            request: $request,
            resource: UserResource::class,
            searchable: ['name', 'email'],
            filterable: ['role'],
            defaultSort: 'created_at',
            defaultOrder: 'desc',
            additionalProps: [
                'roles' => Role::all(['id', 'name'])
            ]
        );
    }

    /**
     * Show the form for creating a new user
     */
    public function create(): Response
    {
        return Inertia::render('Modules/admin/Users/UsersForm', [
            'roles' => Role::all(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created user
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        try {
            $this->userService->createUser($request->validated());

            return redirect()->route('admin.users.index')
                ->with('success', __('users.created'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(int $id): Response
    {
        try {
            $user = $this->userService->getUserById($id);

            return Inertia::render('Modules/admin/Users/UsersForm', [
                'user' => new UserResource($user),
                'roles' => Role::all(['id', 'name']),
            ]);
        } catch (\Exception $e) {
            abort(404, $e->getMessage());
        }
    }

    /**
     * Update the specified user
     */
    public function update(UpdateUserRequest $request, int $id): RedirectResponse
    {
        try {
            $this->userService->updateUser($id, $request->validated());

            return redirect()->route('admin.users.index')
                ->with('success', __('users.updated'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified user
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->userService->deleteUser($id);

            return redirect()->back()
                ->with('success', __('users.deleted'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
}
