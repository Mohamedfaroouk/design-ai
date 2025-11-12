# Inertia.js Migration Guide

## Current Status

✅ **Completed:**
1. Installed Inertia.js packages (Laravel + Vue3)
2. Created `HandleInertiaRequests` middleware
3. Registered Inertia middleware in `bootstrap/app.php`
4. Updated `resources/views/app.blade.php` for Inertia
5. Created `resources/js/app.js` with Inertia setup
6. Created test route `/test-inertia` and `TestPage.vue`
7. Successfully built assets with Vite

## Test Inertia Setup

1. Start the development server:
   ```bash
   composer dev
   # or separately:
   php artisan serve
   npm run dev
   ```

2. Visit: http://localhost:8000/test-inertia

You should see: "Inertia is working!"

## Migration Roadmap

### Phase 1: Authentication System (HIGH PRIORITY)

**Problem:** Current system uses Sanctum tokens (API-first). Inertia uses session-based auth.

**Steps:**
1. Update `config/sanctum.php` - add frontend domain to stateful domains
2. Modify `AuthController` methods:
   - `login()` - Return `Inertia::render()` instead of JSON, use `Auth::attempt()`
   - `logout()` - Use `Auth::logout()` + `Inertia::location('/login')`
   - Remove token generation
3. Update auth routes in `routes/web.php`:
   ```php
   Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
   Route::post('/login', [AuthController::class, 'login']);
   Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');
   ```
4. Update Login.vue:
   - Remove API calls
   - Use Inertia's `useForm()`:
     ```js
     import { useForm } from '@inertiajs/vue3'
     const form = useForm({ email: '', password: '' })
     form.post('/login')
     ```

### Phase 2: Update HasDataTable Trait

**Current:** Returns JSON with pagination meta
**Target:** Return Inertia response with props

**File:** `app/Traits/HasDataTable.php`

```php
protected function dataTableResponse($query, $request, $resource, $searchable = [], $filterable = [])
{
    // ... existing logic ...

    return Inertia::render($pageName, [
        'data' => $resource::collection($items),
        'meta' => [
            'current_page' => $items->currentPage(),
            'last_page' => $items->lastPage(),
            'per_page' => $items->perPage(),
            'total' => $items->total(),
        ],
        'filters' => $request->only(['search', 'sort_by', 'sort_order']),
    ]);
}
```

### Phase 3: Convert Controllers (Systematic Approach)

**Order of conversion:**
1. ✅ Auth (highest priority)
2. Dashboard
3. Users (Admin)
4. Roles (Admin)
5. Settings
6. Products (Client)
7. AI Generation (Client)

**Pattern for each controller:**

**Before (API):**
```php
public function index(Request $request): JsonResponse
{
    return $this->dataTableResponse(
        query: User::with(['roles']),
        request: $request,
        resource: UserResource::class,
        searchable: ['name', 'email'],
    );
}
```

**After (Inertia):**
```php
use Inertia\Inertia;
use Inertia\Response;

public function index(Request $request): Response
{
    return Inertia::render('Modules/admin/Users/UsersIndex', [
        'users' => UserResource::collection(
            User::with(['roles'])
                ->when($request->search, fn($q) =>
                    $q->where('name', 'like', "%{$request->search}%")
                )
                ->paginate(15)
                ->withQueryString()
        ),
    ]);
}

public function create(): Response
{
    return Inertia::render('Modules/admin/Users/UsersForm', [
        'roles' => Role::all(),
    ]);
}

public function store(StoreUserRequest $request): RedirectResponse
{
    $this->service->create($request->validated());
    return redirect()->route('admin.users.index')
        ->with('success', 'User created successfully');
}
```

### Phase 4: Update Vue Pages

**For each page (e.g., UsersIndex.vue):**

1. **Remove:**
   - Pinia store imports
   - API service calls
   - `useFetch()` or `useDataTable()` composables
   - Vue Router imports

2. **Add:**
   ```vue
   <script setup>
   import { Head, Link, router } from '@inertiajs/vue3'
   import AppLayout from '@/components/layout/AppLayout.vue'

   defineProps({
     users: Object,  // Contains data + meta from paginator
   })

   const deleteUser = (id) => {
     if (confirm('Are you sure?')) {
       router.delete(`/admin/users/${id}`)
     }
   }
   </script>

   <template>
     <Head title="Users" />
     <AppLayout>
       <Link href="/admin/users/create" class="btn">Create User</Link>

       <!-- Use users.data for items -->
       <DataTable :data="users.data" :meta="users.meta">
         <!-- ... -->
       </DataTable>
     </AppLayout>
   </template>
   ```

3. **Update navigation:**
   ```vue
   <!-- Before -->
   <router-link to="/admin/users">Users</router-link>

   <!-- After -->
   <Link href="/admin/users">Users</Link>
   ```

4. **Update forms:**
   ```vue
   <script setup>
   import { useForm } from '@inertiajs/vue3'

   const form = useForm({
     name: '',
     email: '',
     password: '',
   })

   const submit = () => {
     form.post('/admin/users', {
       onSuccess: () => form.reset(),
     })
   }
   </script>

   <template>
     <form @submit.prevent="submit">
       <TextInput v-model="form.name" :error="form.errors.name" />
       <Button :loading="form.processing">Save</Button>
     </form>
   </template>
   ```

### Phase 5: Update Layouts

**AppLayout.vue changes:**
1. Remove router view
2. Add default slot:
   ```vue
   <template>
     <div>
       <Sidebar />
       <Topbar />
       <main>
         <slot />  <!-- Inertia renders page here -->
       </main>
     </div>
   </template>
   ```

**AuthLayout.vue changes:**
- Same pattern: replace `<router-view>` with `<slot />`

### Phase 6: Update Shared Components

**DataTable.vue:**
- Remove internal API calls
- Accept `data` and `meta` as props
- Update pagination to use Inertia:
  ```vue
  <button @click="router.get(url, { page: pageNum })">
  ```

**Sidebar.vue / Navigation:**
- Replace all `<router-link>` with `<Link>`
- Update active state detection:
  ```js
  import { usePage } from '@inertiajs/vue3'
  const page = usePage()
  const isActive = computed(() => page.url.startsWith('/admin/users'))
  ```

### Phase 7: Clean Up

**Delete:**
- `resources/js/router/index.js`
- `resources/js/services/` (all API services)
- `resources/js/store/admin/` and `resources/js/store/client/` (keep only `store/index.js` for app settings)
- `resources/js/composables/useDataTable.js` and `useFetch.js`
- `resources/js/main.js` (replaced by app.js)
- `resources/js/App.vue` (not needed with Inertia)

**Keep:**
- `resources/js/store/index.js` (useAppStore, useToastStore)
- `resources/js/composables/useForm.js` - May still be useful for non-Inertia forms
- All components in `resources/js/components/`

### Phase 8: Update Routes

**Move from `routes/api.php` to `routes/web.php`:**

```php
// routes/web.php
use Inertia\Inertia;

Route::middleware(['auth', 'permission:users.view'])->group(function () {
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
});

// Catch-all for 404
Route::fallback(function () {
    return Inertia::render('Errors/404');
});
```

## Key Differences: SPA vs Inertia

| Feature | Old (SPA) | New (Inertia) |
|---------|-----------|---------------|
| Data Fetching | API calls in mounted() | Props from controller |
| Navigation | Vue Router | Inertia Link/router |
| Forms | Axios POST | useForm().post() |
| State | Pinia stores | Inertia page props |
| Auth | Token in localStorage | Session cookies |
| Loading | Manual loading states | form.processing |
| Errors | Manual error handling | form.errors |
| Redirects | router.push() | return redirect() |

## Testing Strategy

1. **Test each page after conversion:**
   - List view (pagination, search, sort)
   - Create form (validation, success)
   - Edit form (load data, update)
   - Delete action (confirmation, success)

2. **Test auth flow:**
   - Login (remember me, errors)
   - Logout (session cleared)
   - Password reset (all steps)
   - Permissions (403 errors)

3. **Test edge cases:**
   - Back button behavior
   - Refresh page (state persists)
   - Direct URL access
   - 404 pages

## Common Pitfalls

1. **Forgetting to return Inertia responses:**
   ```php
   // ❌ Wrong
   public function index() {
       return UserResource::collection(User::all());
   }

   // ✅ Correct
   public function index() {
       return Inertia::render('Users/Index', [
           'users' => UserResource::collection(User::all())
       ]);
   }
   ```

2. **Using old form validation pattern:**
   ```vue
   <!-- ❌ Wrong -->
   <TextInput :error="errors.email" />

   <!-- ✅ Correct -->
   <TextInput :error="form.errors.email" />
   ```

3. **Not using withQueryString() for pagination:**
   ```php
   // ❌ Wrong - loses search params
   ->paginate(15)

   // ✅ Correct
   ->paginate(15)->withQueryString()
   ```

4. **Flash messages not working:**
   ```php
   // Make sure to add to HandleInertiaRequests:
   'flash' => [
       'success' => fn () => $request->session()->get('success'),
       'error' => fn () => $request->session()->get('error'),
   ],
   ```

## Next Steps

1. **Start PHP dev server:** `php artisan serve`
2. **Start Vite:** `npm run dev`
3. **Test Inertia:** Visit http://localhost:8000/test-inertia
4. **Begin conversion:** Start with Phase 1 (Authentication)

## Resources

- [Inertia.js Docs](https://inertiajs.com/)
- [Inertia Vue3 Adapter](https://inertiajs.com/client-side-setup)
- [Laravel Inertia](https://inertiajs.com/server-side-setup)
- [Form Helper](https://inertiajs.com/forms)
- [Validation](https://inertiajs.com/validation)

---

**Estimated Time:** 2-3 weeks full-time (assuming 8 hours/day)
**Current Progress:** 20% complete (Foundation laid)
