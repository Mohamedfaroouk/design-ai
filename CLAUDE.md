# CLAUDE.md

## Project Overview

Laravel 12 + Vue 3 SaaS Dashboard with:
- **Backend**: Laravel 12 (PHP 8.2+), Session-based auth, Spatie permissions
- **Frontend**: Vue 3 + Vite + Tailwind CSS 4.0 + Inertia.js + Pinia (for UI state only)
- **Features**: Dark mode, i18n (EN/AR), RTL/LTR, DataTable trait
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
│   ├── Admin/                      # Admin controllers (use HasDataTableInertia trait)
│   └── Client/                     # Client controllers (use HasDataTableInertia trait)
├── Services/
│   ├── Admin/                      # Admin business logic
│   └── Client/                     # Client business logic
├── Http/Requests/
│   ├── Admin/                      # Admin validation rules
│   └── Client/                     # Client validation rules
├── Http/Resources/
│   ├── Admin/                      # Admin API responses (for Inertia props)
│   └── Client/                     # Client API responses (for Inertia props)
└── Models/                         # Eloquent models

resources/js/
├── pages/
│   ├── Modules/
│   │   ├── admin/                  # Admin Vue pages (Users, Roles, Settings)
│   │   └── client/                 # Client Vue pages (ImageWizard, etc.)
│   ├── auth/                       # Auth pages (Login, ForgotPassword, etc.)
│   ├── Profile/                    # Profile pages
│   └── Dashboard.vue               # Main dashboard
├── store/
│   ├── index.js                    # App store (dark mode, direction) + Toast store
│   ├── admin/                      # Admin Pinia stores (optional, for complex state)
│   └── client/                     # Client Pinia stores (optional, for complex state)
├── components/
│   ├── inputs/                     # Input components
│   ├── tables/                     # Table components
│   └── ui/                         # UI components
├── composables/                    # Reusable composables (useImageUpload, etc.)
└── i18n/locales/                   # Translations (en.json, ar.json)
```

## Backend Architecture

### Required Components (Admin/Client separation)

**Every module needs 4 files:**
1. **Controller** - HTTP handling (use `HasDataTableInertia` trait for index)
2. **Service** - Business logic (DB transactions, file uploads)
3. **Request** - Validation (Store/Update)
4. **Resource** - Inertia props formatting

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
Route::middleware(['auth', 'permission:products.view'])
    ->get('/admin/products', [WebProductController::class, 'index']);
```

**Default roles:** `admin` (all permissions), `client` (basic access)

### Code Pattern Example

**Controller (use HasDataTableInertia):**
```php
use App\Traits\HasDataTableInertia;
use Inertia\Inertia;
use Inertia\Response;

public function index(Request $request): Response {
    return $this->inertiaDataTable(
        page: 'Modules/admin/Products/ProductsIndex',
        query: Product::with(['category']),
        request: $request,
        resource: ProductResource::class,
        searchable: ['name', 'sku', 'category.name'],
        filterable: ['category_id', 'status']
    );
}

public function create(): Response {
    return Inertia::render('Modules/admin/Products/ProductsForm');
}

public function store(StoreProductRequest $request): RedirectResponse {
    try {
        $this->service->create($request->validated());
        return redirect()->route('admin.products.index')
            ->with('success', __('messages.product.created'));
    } catch (\Exception $e) {
        return redirect()->back()
            ->withInput()
            ->withErrors(['error' => $e->getMessage()]);
    }
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

// For Inertia, ensure failedValidation redirects back with errors
protected function failedValidation(Validator $validator) {
    throw new HttpResponseException(
        back()->withErrors($validator->errors())->withInput()
    );
}
```

**Artisan commands:**
```bash
php artisan make:controller Admin/WebProductController
php artisan make:request Admin/StoreProductRequest
php artisan make:resource Admin/ProductResource
# Service: create manually in app/Services/Admin/
```

### DataTable System

**Laravel trait:** `app/Traits/HasDataTableInertia.php`
- Pagination, search, sort, filter (supports nested relations like `category.name`)
- Query params: `?page=1&search=query&sort_by=name&category_id=5`
- Returns Inertia response with data and meta as props

**Frontend:** Data comes from Inertia props
```vue
<script setup>
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const products = computed(() => page.props.products || [])
const meta = computed(() => page.props.meta || {})
</script>
```

### Authentication (Session-based)

**Default users:**
- **Admin:** `admin@example.com` / `password` (⚠️ change in production)
- **Client:** `client@example.com` / `password` (⚠️ change in production)

**Routes:**
- `GET /login` → Show login form
- `POST /login` → Authenticate user
- `POST /logout` → Logout user
- `GET /forgot-password` → Show forgot password form
- `POST /forgot-password` → Send OTP
- `POST /verify-otp` → Verify OTP
- `POST /reset-password` → Reset password

**User data:** Available via `page.props.auth.user` in all Inertia pages

### Translations (EN/AR)

**Backend:** `lang/{en|ar}/auth.php`, use `__('auth.login.success')`
**Frontend:** `resources/js/i18n/locales/{en|ar}.json`, use `$t('users.title')`

**Locale Middleware:** `app/Http/Middleware/SetLocale.php`
- Priority: `?lang=ar` → Accept-Language header → User preference → Default (en)
- Auto-registered in `bootstrap/app.php` for web routes

**Frontend:** Locale shared via Inertia props
- Available as `page.props.locale`
- Translations loaded via `page.props.translations`

## Vue 3 Frontend (Inertia.js)

**Admin/Client separation:** Match backend structure
- **Admin Pages:** `pages/Modules/admin/Users/UsersIndex.vue`, `UsersForm.vue`
- **Client Pages:** `pages/Modules/client/ImageWizard.vue`
- **Stores:** Only for UI state (dark mode, toast) - NOT for data fetching

### Components & Composables

**Inputs (all with dark mode):** `TextInput`, `Select`, `DatePicker`, `ImagePicker`, etc.
**UI:** `Button` (variants: primary/secondary/danger), `Modal`, `Toast`, `Spinner`
**Table:** `DataTable` (uses props from Inertia)

**Composables:**
```js
// Image upload (uses axios directly for file uploads)
const { preview, uploading, progress, upload } = useImageUpload()
await upload(file, '/client/uploads')
```

**Pinia stores (UI state only):**
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

1. **Controller** - `app/Http/Controllers/Admin|Client/WebProductController.php`
   ```bash
   php artisan make:controller Admin/WebProductController
   ```
   - Use `HasDataTableInertia` trait for index method
   - Return `Inertia::render()` or `redirect()` responses
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
   - Override `failedValidation()` for Inertia

4. **Resource** - `app/Http/Resources/Admin|Client/ProductResource.php`
   ```bash
   php artisan make:resource Admin/ProductResource
   ```
   - Inertia props formatting

5. **Routes** - Add to `routes/web.php` with permissions
   ```php
   Route::middleware(['auth', 'permission:products.view'])->group(function () {
       Route::resource('products', WebProductController::class);
   });
   ```

#### Frontend (2 files required):

1. **Index Page** - `resources/js/pages/Modules/admin|client/Products/ProductsIndex.vue`
   ```vue
   <script setup>
   import { computed } from 'vue'
   import { Head, Link, usePage } from '@inertiajs/vue3'
   import { useI18n } from 'vue-i18n'
   import { useAppStore } from '@/store/index'
   import DataTable from '@/components/tables/DataTable.vue'

   const { t } = useI18n()
   const page = usePage()
   const appStore = useAppStore()

   // Data comes from Inertia props
   const products = computed(() => page.props.products || [])
   const meta = computed(() => page.props.meta || {})

   // Table columns
   const columns = computed(() => [
     { key: 'name', label: t('products.fields.name'), sortable: true },
     { key: 'created_at', label: t('products.fields.createdAt'), sortable: true }
   ])
   </script>

   <template>
     <Head :title="$t('products.title')" />
     <DataTable
       :columns="columns"
       :data="products"
       :meta="meta"
     />
   </template>
   ```

2. **Form Page** - `resources/js/pages/Modules/admin|client/Products/ProductsForm.vue`
   ```vue
   <script setup>
   import { Head, useForm } from '@inertiajs/vue3'
   import { useI18n } from 'vue-i18n'
   import { useToastStore } from '@/store/index'
   import TextInput from '@/components/inputs/TextInput.vue'
   import Button from '@/components/ui/Button.vue'

   const { t } = useI18n()
   const toast = useToastStore()
   const page = usePage()

   // Use Inertia's useForm
   const form = useForm({
     name: page.props.product?.name || '',
     email: page.props.product?.email || '',
   })

   const handleSubmit = () => {
     const url = page.props.product 
       ? `/admin/products/${page.props.product.id}`
       : '/admin/products'
     
     const method = page.props.product ? 'put' : 'post'
     
     form[method](url, {
       preserveScroll: true,
       onSuccess: () => {
         toast.success(t('products.saved'))
       },
       onError: (errors) => {
         // Errors automatically available in form.errors
       }
     })
   }
   </script>

   <template>
     <Head :title="$t('products.form.title')" />
     <form @submit.prevent="handleSubmit">
       <TextInput
         v-model="form.name"
         :label="$t('products.fields.name')"
         :error="form.errors.name"
       />
       <Button type="submit" :loading="form.processing">
         {{ $t('common.save') }}
       </Button>
     </form>
   </template>
   ```

3. **Translations** - Add to `resources/js/i18n/locales/en.json` and `ar.json`

#### Key Patterns (MUST FOLLOW):

**❌ DON'T use API services or Pinia stores for data:**
```js
// ❌ WRONG - No API services
import productsService from '@/services/admin/products'
await productsService.fetchList()

// ❌ WRONG - No Pinia stores for data
const productsStore = useAdminProductsStore()
await productsStore.fetchList()
```

**✅ DO use Inertia props:**
```js
// ✅ CORRECT - Data from Inertia props
const page = usePage()
const products = computed(() => page.props.products || [])
```

**✅ DO use Inertia's useForm:**
```js
// ✅ CORRECT - Inertia form handling
import { useForm } from '@inertiajs/vue3'
const form = useForm({ name: '', email: '' })
form.post('/admin/products', {
  onSuccess: () => router.visit('/admin/products')
})
```

**✅ DO use Inertia router for navigation:**
```js
// ✅ CORRECT - Inertia navigation
import { router, Link } from '@inertiajs/vue3'
router.visit('/admin/products')
// or
<Link href="/admin/products">Products</Link>
```

**Structure Reference:**
- **Admin Example:** `pages/Modules/admin/Users/UsersIndex.vue`
- **Client Example:** `pages/Modules/client/Products/ProductsIndex.vue`

**Checklist:**
- ✅ Backend: Controller, Service, Request, Resource, Routes
- ✅ Frontend: Index page (uses Inertia props)
- ✅ Frontend: Form page (uses Inertia's useForm)
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

### ✅ DO: Use Inertia's `useForm` for forms
```vue
<script setup>
import { useForm } from '@inertiajs/vue3'

const form = useForm({ email: '', password: '' })

const handleSubmit = () => {
  form.post('/admin/users', {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('User created')
    },
    onError: (errors) => {
      // Errors automatically available in form.errors
    }
  })
}
</script>

<template>
  <TextInput v-model="form.email" :error="form.errors.email" />
  <Button @click="handleSubmit" :loading="form.processing">Submit</Button>
</template>
```

### ❌ DON'T: Use API calls or manual error handling
```js
// ❌ WRONG - Don't use API services
import api from '@/services/api'
await api.post('/admin/users', data)

// ❌ WRONG - Don't manually handle errors
catch (error) {
  if (error.errors) {
    Object.keys(error.errors).forEach(key => {
      errors.value[key] = error.errors[key]
    })
  }
}
```

### How Error Handling Works
1. **Validation errors** → Automatically available in `form.errors` (no toast)
2. **Other errors (401, 403, 500, etc.)** → Can be handled in `onError` callback
3. **Backend message preserved** → Custom validation messages display correctly
4. **Automatic error clearing** → Errors reset on next submit

## Inertia.js Patterns

### Navigation
```js
import { router, Link } from '@inertiajs/vue3'

// Programmatic navigation
router.visit('/admin/products')
router.get('/admin/products', { search: 'query' })
router.post('/admin/products', formData)
router.put(`/admin/products/${id}`, formData)
router.delete(`/admin/products/${id}`)

// Link component
<Link href="/admin/products">Products</Link>
<Link href="/admin/products" :data="{ search: 'query' }">Search</Link>
```

### Form Handling
```js
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  name: '',
  email: ''
})

// Submit form
form.post('/admin/users', {
  preserveScroll: true,
  preserveState: false,
  onSuccess: (page) => {
    // Handle success
  },
  onError: (errors) => {
    // Handle errors (optional, form.errors already populated)
  },
  onFinish: () => {
    // Always called
  }
})

// Access form state
form.processing  // Boolean - is submitting
form.errors      // Object - validation errors
form.hasErrors   // Boolean - has any errors
```

### Accessing Props
```js
import { usePage } from '@inertiajs/vue3'

const page = usePage()

// Access props
const user = computed(() => page.props.auth?.user)
const products = computed(() => page.props.products || [])
const flash = computed(() => page.props.flash)

// Access URL
const currentUrl = computed(() => page.url)
```

### Flash Messages
```php
// Backend
return redirect()->route('admin.products.index')
    ->with('success', __('messages.product.created'));
```

```vue
<!-- Frontend -->
<script setup>
import { usePage } from '@inertiajs/vue3'
import { watch } from 'vue'
import { useToastStore } from '@/store/index'

const page = usePage()
const toast = useToastStore()

watch(() => page.props.flash?.success, (message) => {
  if (message) {
    toast.success(message)
  }
})
</script>
```
