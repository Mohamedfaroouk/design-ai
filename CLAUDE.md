# CLAUDE.md

## Project Overview

Laravel 12 + Vue 3 SaaS Dashboard with:
- **Backend**: Laravel 12 (PHP 8.2+), Sanctum auth, Spatie permissions
- **Frontend**: Vue 3 + Vite + Tailwind CSS 4.0 + Pinia + Vue Router
- **Features**: Dark mode, i18n (EN/AR), RTL/LTR, DataTable composable
- **Database**: SQLite, queue system

## Development Commands

```bash
composer dev          # Start all services (serve + queue + pail + vite)
npm run dev           # Vite dev server only
composer test         # Run tests
npm run build         # Production build
vendor/bin/pint       # Format code
```

## Project Structure

```
app/
├── Http/Controllers/Admin|Client/  # Controllers (use HasDataTable trait)
├── Services/Admin|Client/          # Business logic
├── Http/Requests/Admin|Client/     # Validation
├── Http/Resources/Admin|Client/    # API responses
└── Models/                         # Eloquent models

resources/js/
├── pages/admin|client/modules/     # Vue pages
├── services/admin|client/          # API services
├── store/admin|client/             # Pinia stores
├── components/inputs|tables|ui/    # Reusable components
└── i18n/locales/                   # Translations (en.json, ar.json)
```

## Backend Architecture

### Required Components (Admin/Client separation)

**Every module needs 4 files:**
1. **Controller** - HTTP handling (use `HasDataTable` trait for index)
2. **Service** - Business logic (DB transactions, file uploads)
3. **Request** - Validation (Store/Update)
4. **Resource** - API responses

**❌ NO business logic in Controllers**
**✅ ALL business logic in Services**

### Permissions (Spatie)

**Register in `bootstrap/app.php`:**
```php
$middleware->alias([
    'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
]);
```

**Use in routes (Laravel 11+):**
```php
Route::middleware(['auth:sanctum', 'permission:products.view'])
    ->get('/admin/products', [ProductController::class, 'index']);
```

**Default roles:** `admin` (all permissions), `client` (basic access)

### Code Pattern Example

**Controller (use HasDataTable):**
```php
use App\Traits\HasDataTable;

public function index(Request $request): JsonResponse {
    return $this->dataTableResponse(
        query: Product::with(['category']),
        request: $request,
        resource: ProductResource::class,
        searchable: ['name', 'sku', 'category.name'],
        filterable: ['category_id', 'status']
    );
}

public function store(StoreRequest $request): JsonResponse {
    return response()->json([
        'data' => new ProductResource($this->service->create($request->validated()))
    ], 201);
}
```

**Service (DB transactions):**
```php
public function create(array $data): Product {
    return DB::transaction(fn() => Product::create($data));
}
```

**Request (authorize + validate):**
```php
public function authorize(): bool {
    return $this->user()->can('products.create');
}

public function rules(): array {
    return ['name' => ['required', 'string', 'max:255']];
}
```

**Artisan commands:**
```bash
php artisan make:controller Admin/ProductController --api
php artisan make:request Admin/StoreProductRequest
php artisan make:resource Admin/ProductResource
# Service: create manually in app/Services/Admin/
```

### DataTable System

**Laravel trait:** `app/Traits/HasDataTable.php`
- Pagination, search, sort, filter (supports nested relations like `category.name`)
- Query params: `?page=1&search=query&sort_by=name&category_id=5`

**Vue composable:** `resources/js/composables/useDataTable.js`
```js
const { items, meta, loading, handleSearch, handleSort, handlePageChange, refresh } =
  useDataTable(productsService.fetchList, { perPage: 15, sortBy: 'created_at' })
```
- Auto state management, URL sync, debounced search (300ms default)
- Use with `<DataTable>` component for full functionality

**DataTable with filters:**
```vue
<DataTable
  :filterable="true"
  @filter="handleFilter"
>
  <template #filters="{ filters, updateFilter }">
    <Select
      :modelValue="filters.role"
      @update:modelValue="updateFilter('role', $event)"
      :label="$t('users.fields.role')"
      :options="roleOptions"
    />
  </template>
</DataTable>
```

**Filter handler:**
```js
const handleFilter = (filterData) => {
  filters.value = { ...filters.value, ...filterData, page: 1 }
  loadUsers()
}
```

### Authentication (Sanctum + OTP)

**Default login:** `admin@example.com` / `password` (⚠️ change in production)

**Endpoints:**
- `POST /api/auth/login` → returns `{ user, token }`
- `POST /api/auth/forgot-password` → sends OTP (6 digits, 10min expiry)
- `POST /api/auth/verify-otp` → validates OTP
- `POST /api/auth/reset-password` → resets password
- `GET /api/auth/me` → current user + permissions
- `POST /api/auth/logout`

**Use token:** `Authorization: Bearer {token}`

### Translations (EN/AR)

**Backend:** `lang/{en|ar}/auth.php`, use `__('auth.login.success')`
**Frontend:** `resources/js/i18n/locales/{en|ar}.json`, use `$t('users.title')`

**Locale Middleware:** `app/Http/Middleware/SetLocale.php`
- Priority: `?lang=ar` → Accept-Language header → User preference → Default (en)
- Auto-registered in `bootstrap/app.php` for API routes

**Frontend:** Sends Accept-Language header with every request
- Reads from `localStorage.getItem('locale')`
- Updated via `<LanguageSwitcher>` component

## Vue 3 Frontend

**Admin/Client separation:** Match backend structure
- Pages: `pages/admin|client/modules/users/Index.vue`, `Form.vue`
- Services: `services/admin|client/users.js`
- Stores: `store/admin|client/users.js`

### Components & Composables

**Inputs (all with dark mode):** `TextInput`, `Select`, `DatePicker`, `ImagePicker`, etc.
**UI:** `Button` (variants: primary/secondary/danger), `Modal`, `Toast`, `Spinner`
**Table:** `DataTable` (search, sort, paginate)

**Composables:**
```js
// Form handling
const { form, errors, getError, post, put } = useForm({ name: '', email: '' })
await post('/api/users', {
  successMessage: 'User created',
  onSuccess: () => router.push('/users')
})

// Display errors in template
<TextInput :error="getError('email')" />

// Data fetching
const { data, loading, refresh } = useFetch('/api/users')

// Toasts
const toast = useToastStore()
toast.success('Saved!')
```

**Pinia stores:**
```js
const appStore = useAppStore()
appStore.toggleDarkMode()  // Dark mode
appStore.setDirection('rtl')  // RTL/LTR
```

**RTL Support:** Use `ms`/`me` (margin-start/end) instead of `ml`/`mr`

### Creating New Modules - Quick Steps

**Backend:**
1. Controller (HasDataTable), Service, Request, Resource in `app/.../Admin|Client/`
2. Routes in `routes/api.php` with permissions

**Frontend:**
1. Service in `services/admin|client/products.js`
2. Store in `store/admin|client/products.js` (optional)
3. Index page with `useDataTable` + `<DataTable>`
4. Form page with `useForm` + input components
5. Routes in `router/admin.js`
6. Add menu item + translations

**Checklist:** Controller, Service, Request, Resource, routes, service, pages, router, translations (EN/AR), dark mode support

## Development Standards (CRITICAL)

**Every new feature MUST have:**

**1. 🌙 Dark Mode:**
```vue
:class="appStore.darkMode ? 'bg-gray-800 text-gray-100' : 'bg-white text-gray-900'"
```
- Use `gray-800/900` (dark) vs `white/gray-50` (light)
- Add `transition-colors` for smooth switching
- See `COLOR_SYSTEM.md` for color system

**2. 🌐 Translations:**
```vue
{{ $t('users.title') }}  <!-- Always use $t() -->
```
- Update `en.json` + `ar.json`
- Never hardcode text

**3. 📊 Use Existing Components:**
- ✅ `<TextInput>`, `<Select>`, `<DataTable>`, `<Button>`
- ❌ Raw `<input>`, `<table>`, `<button>`

**4. 🎨 RTL Support:**
- ✅ Use `ms`/`me`, `ps`/`pe`, `start`/`end`
- ❌ Don't use `ml`/`mr`, `pl`/`pr`, `left`/`right`

## Error Handling Best Practices

### ✅ DO: Use `useForm` composable for forms
```vue
<script setup>
const { form, errors, getError, post, put } = useForm({ email: '', password: '' })

const handleSubmit = async () => {
  try {
    await post('/api/users', {
      successMessage: 'User created',
      onSuccess: () => router.push('/users')
    })
  } catch (error) {
    // Errors are auto-handled:
    // - Field errors shown inline via getError()
    // - Non-422 errors show toast
  }
}
</script>

<template>
  <TextInput v-model="form.email" :error="getError('email')" />
  <Button @click="handleSubmit" :loading="loading">Submit</Button>
</template>
```

### ❌ DON'T: Manually handle errors
```js
// ❌ WRONG - Don't manually set errors
catch (error) {
  if (error.errors) {
    Object.keys(error.errors).forEach(key => {
      errors.value[key] = error.errors[key]
    })
  }
  toast.error(error.message)
}

// ✅ CORRECT - useForm handles it automatically
catch (error) {
  // Leave empty or add custom logic only if needed
}
```

### How Error Handling Works
1. **422 Validation errors** → Shown inline under fields (no toast)
2. **Other errors (401, 403, 500, etc.)** → Toast notification
3. **Backend message preserved** → Custom validation messages display correctly
4. **Automatic error clearing** → Errors reset on next submit
