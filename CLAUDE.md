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
├── Http/Controllers/
│   ├── Admin/                      # Admin controllers (use HasDataTable trait)
│   └── Client/                     # Client controllers (use HasDataTable trait)
├── Services/
│   ├── Admin/                      # Admin business logic
│   └── Client/                     # Client business logic
├── Http/Requests/
│   ├── Admin/                      # Admin validation rules
│   └── Client/                     # Client validation rules
├── Http/Resources/
│   ├── Admin/                      # Admin API responses
│   └── Client/                     # Client API responses
└── Models/                         # Eloquent models

resources/js/
├── pages/
│   ├── Modules/
│   │   ├── admin/                  # Admin Vue pages (Users, Roles, Settings)
│   │   └── client/                 # Client Vue pages (ImageWizard, etc.)
│   ├── auth/                       # Auth pages (Login, ForgotPassword, etc.)
│   ├── Profile/                    # Profile pages
│   └── Dashboard.vue               # Main dashboard
├── services/
│   ├── admin/                      # Admin API services
│   └── client/                     # Client API services
├── store/
│   ├── admin/                      # Admin Pinia stores
│   └── client/                     # Client Pinia stores
├── components/
│   ├── inputs/                     # Input components
│   ├── tables/                     # Table components
│   └── ui/                         # UI components
├── composables/                    # Reusable composables
├── i18n/locales/                   # Translations (en.json, ar.json)
└── router/                         # Vue Router configuration
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
// ✅ CORRECT - Named import
import { useDataTable } from '@/composables/useDataTable'

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

**Default users:**
- **Admin:** `admin@example.com` / `password` (⚠️ change in production)
- **Client:** `client@example.com` / `password` (⚠️ change in production)

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
- **Admin Pages:** `pages/Modules/admin/Users/UsersIndex.vue`, `UsersForm.vue`
- **Client Pages:** `pages/Modules/client/ImageWizard.vue`
- **Services:** `services/admin/users.js`, `services/client/aiImage.js`
- **Stores:** `store/admin/users.js`, `store/client/ai.js`

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
// ✅ CORRECT - Import from store/index
import { useAppStore } from '@/store/index'
import { useToastStore } from '@/store/index'

const appStore = useAppStore()
appStore.toggleDarkMode()  // Dark mode
appStore.setDirection('rtl')  // RTL/LTR

const toast = useToastStore()
toast.success('Saved!')
```

**⚠️ IMPORTANT - Store Imports:**
- ✅ Always import from `@/store/index` for `useAppStore` and `useToastStore`
- ❌ Don't use `@/store/app` or `@/store/toast` - these files don't exist
- Both stores are exported from the single `store/index.js` file

**RTL Support:** Use `ms`/`me` (margin-start/end) instead of `ml`/`mr`

### Creating New Modules - REQUIRED STRUCTURE

⚠️ **CRITICAL: ALL features MUST follow this exact structure**

#### Backend (4 files required):

1. **Controller** - `app/Http/Controllers/Admin|Client/ProductController.php`
   ```bash
   php artisan make:controller Admin/ProductController --api
   ```
   - Use `HasDataTable` trait for index method
   - Keep it thin - only HTTP handling

2. **Service** - `app/Services/Admin|Client/ProductService.php`
   ```bash
   # Create manually
   ```
   - ALL business logic goes here
   - Use DB transactions
   - Handle file uploads

3. **Request** - `app/Http/Requests/Admin|Client/StoreProductRequest.php`
   ```bash
   php artisan make:request Admin/StoreProductRequest
   ```
   - Validation rules
   - Authorization logic

4. **Resource** - `app/Http/Resources/Admin|Client/ProductResource.php`
   ```bash
   php artisan make:resource Admin/ProductResource
   ```
   - API response formatting

5. **Routes** - Add to `routes/api.php` with permissions

#### Frontend (3 files required):

1. **Service** - `resources/js/services/admin|client/products.js`
   ```js
   import api from '../api'

   export default {
     fetchList(params = {}) {
       return api.get('/admin/products', params)  // ✅ Pass params directly
     },
     fetchOne(id) {
       return api.get(`/admin/products/${id}`)
     },
     create(data) {
       return api.post('/admin/products', data)
     },
     update(id, data) {
       return api.put(`/admin/products/${id}`, data)
     },
     delete(id) {
       return api.delete(`/admin/products/${id}`)
     }
   }
   ```
   **⚠️ IMPORTANT:** Always pass `params` directly to `api.get()`, NOT `{ params }`

2. **Pinia Store** - `resources/js/store/admin|client/products.js` **(REQUIRED)**
   ```js
   import { defineStore } from 'pinia'
   import productsService from '@/services/admin/products'

   export const useAdminProductsStore = defineStore('adminProducts', {
     state: () => ({
       products: [],
       meta: null,
       loading: false,
       error: null
     }),

     actions: {
       async fetchList(params = {}) {
         this.loading = true
         this.error = null
         try {
           const response = await productsService.fetchList(params)
           this.products = response.data
           this.meta = response.meta
           return response
         } catch (error) {
           this.error = error.message
           throw error
         } finally {
           this.loading = false
         }
       },

       async create(data) {
         this.loading = true
         try {
           const response = await productsService.create(data)
           return response
         } catch (error) {
           this.error = error.message
           throw error
         } finally {
           this.loading = false
         }
       },

       async update(id, data) {
         this.loading = true
         try {
           const response = await productsService.update(id, data)
           return response
         } catch (error) {
           this.error = error.message
           throw error
         } finally {
           this.loading = false
         }
       },

       async delete(id) {
         this.loading = true
         try {
           const response = await productsService.delete(id)
           this.products = this.products.filter(p => p.id !== id)
           return response
         } catch (error) {
           this.error = error.message
           throw error
         } finally {
           this.loading = false
         }
       }
     }
   })
   ```

3. **Index Page** - `resources/js/pages/Modules/admin|client/Products/ProductsIndex.vue`
   ```vue
   <script setup>
   import { ref, computed, onMounted } from 'vue'
   import { useI18n } from 'vue-i18n'
   import { useAppStore } from '@/store/index'
   import { useToastStore } from '@/store/index'
   import { useAdminProductsStore } from '@/store/admin/products'
   import DataTable from '@/components/tables/DataTable.vue'

   const { t } = useI18n()
   const appStore = useAppStore()
   const toast = useToastStore()
   const productsStore = useAdminProductsStore()

   // Filters state
   const filters = ref({
     search: '',
     sortBy: 'created_at',
     sortOrder: 'desc',
     page: 1,
     perPage: 15
   })

   // Table columns
   const columns = computed(() => [
     { key: 'name', label: t('products.fields.name'), sortable: true },
     { key: 'created_at', label: t('products.fields.createdAt'), sortable: true }
   ])

   // Load data
   const loadProducts = async () => {
     try {
       await productsStore.fetchList(filters.value)
     } catch (error) {
       toast.error(error.message || t('common.error'))
     }
   }

   // Event handlers
   const handleSearch = (query) => {
     filters.value.search = query
     filters.value.page = 1
     loadProducts()
   }

   const handleSort = ({ column, order }) => {
     filters.value.sortBy = column
     filters.value.sortOrder = order
     loadProducts()
   }

   const handlePageChange = (page) => {
     filters.value.page = page
     loadProducts()
   }

   onMounted(() => {
     loadProducts()
   })
   </script>

   <template>
     <DataTable
       :columns="columns"
       :data="productsStore.products"
       :meta="productsStore.meta"
       :loading="productsStore.loading"
       @search="handleSearch"
       @sort="handleSort"
       @page-change="handlePageChange"
     />
   </template>
   ```

4. **Form Page** - `resources/js/pages/Modules/admin|client/Products/ProductsForm.vue`
   - Use `useForm` composable for form handling
   - See "Error Handling Best Practices" section

5. **Router** - Add routes to `resources/js/router/index.js`

6. **Translations** - Add to `resources/js/i18n/locales/en.json` and `ar.json`

#### Key Patterns (MUST FOLLOW):

**❌ DON'T use `useDataTable` composable:**
```js
// ❌ WRONG
const { items, meta } = useDataTable(service.fetchList)
```

**✅ DO use Pinia store pattern:**
```js
// ✅ CORRECT
const productsStore = useAdminProductsStore()
const filters = ref({ page: 1, search: '' })
await productsStore.fetchList(filters.value)
```

**Structure Reference:**
- **Admin Example:** `pages/Modules/admin/Roles/RolesIndex.vue` + `store/admin/roles.js`
- **Client Example:** `pages/Modules/client/AiGenerationIndex.vue` + `store/client/aiGeneration.js`

**Checklist:**
- ✅ Backend: Controller, Service, Request, Resource, Routes
- ✅ Frontend: Service file (correct params handling)
- ✅ Frontend: Pinia store (state + actions)
- ✅ Frontend: Index page (store pattern, not useDataTable)
- ✅ Frontend: Form page (useForm composable)
- ✅ Router configuration
- ✅ Translations (EN/AR)
- ✅ Dark mode support
- ✅ Import stores from `@/store/index` for useAppStore/useToastStore

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
