# Vue 3 Dashboard with Inertia.js - Quick Start Guide

## Overview

This Laravel + Vue 3 SaaS Dashboard uses **Inertia.js** to bridge Laravel and Vue seamlessly:

✅ Complete Vue 3 setup with Composition API
✅ Inertia.js for server-driven UI (no API layer needed)
✅ Session-based authentication
✅ Pinia for UI state only (dark mode, toast)
✅ 8 reusable input components
✅ DataTable with server-side pagination
✅ Complete Users CRUD module
✅ RTL/LTR support
✅ Toast notifications
✅ Modal dialogs
✅ Form validation (automatic via Inertia)
✅ Image upload with preview

## Getting Started

### 1. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install NPM dependencies
npm install
```

### 2. Configure Environment

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate
```

### 3. Start Development

```bash
# Option 1: Run everything with one command
composer dev

# Option 2: Run services individually
php artisan serve    # Terminal 1
npm run dev         # Terminal 2
```

### 4. Access the Dashboard

Open your browser and navigate to:
```
http://localhost:8000
```

You should see the Vue dashboard with:
- **Dashboard** page with statistics
- **Users** page with CRUD operations

## Project Structure

### Components

**Input Components** (`resources/js/components/inputs/`)
- `TextInput.vue` - Text/email/password input
- `Textarea.vue` - Multiline text input
- `NumberInput.vue` - Number input with validation
- `DatePicker.vue` - Date picker using Flatpickr
- `Select.vue` - Dropdown with search support
- `MultiSelect.vue` - Multiple selection dropdown
- `FileInput.vue` - File upload with drag & drop
- `ImagePicker.vue` - Image upload with preview

**UI Components** (`resources/js/components/ui/`)
- `Button.vue` - Button with variants and loading state
- `Modal.vue` - Modal dialog with slots
- `Toast.vue` - Toast notifications
- `Spinner.vue` - Loading spinner

**Layout Components** (`resources/js/components/layout/`)
- `AppLayout.vue` - Main layout wrapper (uses Inertia slots)
- `Sidebar.vue` - Collapsible sidebar navigation (uses Inertia Link)
- `Topbar.vue` - Top navigation bar with user menu (uses Inertia router)

**Table Components** (`resources/js/components/tables/`)
- `DataTable.vue` - Full-featured data table with:
  - Server-side pagination (data from Inertia props)
  - Sorting
  - Search
  - Custom cell rendering
  - Actions column

### Composables

**useImageUpload** - Image upload with preview
```js
import { useImageUpload } from '@/composables/useImageUpload'

const { preview, uploading, progress, upload } = useImageUpload()
await upload(file, '/client/uploads')
```

**Note:** `useForm` and `useFetch` are provided by Inertia.js:
```js
import { useForm, usePage } from '@inertiajs/vue3'

// Form handling
const form = useForm({ name: '', email: '' })
form.post('/admin/users', {
  onSuccess: () => router.visit('/admin/users')
})

// Access page props
const page = usePage()
const user = computed(() => page.props.auth?.user)
```

### State Management

**Toast Store** - Show notifications
```js
import { useToastStore } from '@/store/index'
const toast = useToastStore()

toast.success('Success message')
toast.error('Error message')
toast.warning('Warning message')
toast.info('Info message')
```

**App Store** - App-level UI state
```js
import { useAppStore } from '@/store/index'
const appStore = useAppStore()

appStore.toggleDarkMode()
appStore.setDirection('rtl') // or 'ltr'
```

**⚠️ Important:** Pinia stores are ONLY for UI state (dark mode, toast). Data comes from Inertia props, not stores.

## Creating a New Module

Follow this example to create a "Products" module:

### 1. Create Backend Controller

`app/Http/Controllers/Admin/WebProductController.php`
```php
use App\Traits\HasDataTableInertia;
use Inertia\Inertia;
use Inertia\Response;

class WebProductController extends Controller
{
    use HasDataTableInertia;

    public function index(Request $request): Response
    {
        return $this->inertiaDataTable(
            page: 'Modules/admin/Products/ProductsIndex',
            query: Product::query(),
            request: $request,
            resource: ProductResource::class,
            searchable: ['name', 'sku'],
            filterable: ['status']
        );
    }

    public function create(): Response
    {
        return Inertia::render('Modules/admin/Products/ProductsForm');
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());
        
        return redirect()->route('admin.products.index')
            ->with('success', __('messages.product.created'));
    }
}
```

### 2. Create Service

`app/Services/Admin/ProductService.php`
```php
class ProductService
{
    public function create(array $data): Product
    {
        return DB::transaction(fn() => Product::create($data));
    }
}
```

### 3. Create Request

`app/Http/Requests/Admin/StoreProductRequest.php`
```php
class StoreProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'unique:products,sku'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            back()->withErrors($validator->errors())->withInput()
        );
    }
}
```

### 4. Create Resource

`app/Http/Resources/Admin/ProductResource.php`
```php
class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            'created_at' => $this->created_at,
        ];
    }
}
```

### 5. Add Routes

In `routes/web.php`:
```php
Route::middleware(['auth', 'permission:products.view'])->group(function () {
    Route::resource('products', WebProductController::class);
});
```

### 6. Create Frontend Pages

**Index Page** - `resources/js/pages/Modules/admin/Products/ProductsIndex.vue`
```vue
<script setup>
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import DataTable from '@/components/tables/DataTable.vue'

const { t } = useI18n()
const page = usePage()

// Data comes from Inertia props
const products = computed(() => page.props.products || [])
const meta = computed(() => page.props.meta || {})

const columns = computed(() => [
  { key: 'name', label: t('products.fields.name'), sortable: true },
  { key: 'sku', label: t('products.fields.sku'), sortable: true },
])
</script>

<template>
  <Head :title="$t('products.title')" />
  
  <div class="mb-4">
    <Link href="/admin/products/create" class="btn-primary">
      {{ $t('products.create') }}
    </Link>
  </div>

  <DataTable
    :columns="columns"
    :data="products"
    :meta="meta"
  />
</template>
```

**Form Page** - `resources/js/pages/Modules/admin/Products/ProductsForm.vue`
```vue
<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
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
  sku: page.props.product?.sku || '',
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
      router.visit('/admin/products')
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
      required
    />
    
    <TextInput
      v-model="form.sku"
      :label="$t('products.fields.sku')"
      :error="form.errors.sku"
      required
    />
    
    <Button type="submit" :loading="form.processing">
      {{ $t('common.save') }}
    </Button>
  </form>
</template>
```

### 7. Add Translations

In `resources/js/i18n/locales/en.json`:
```json
{
  "products": {
    "title": "Products",
    "create": "Create Product",
    "saved": "Product saved successfully",
    "fields": {
      "name": "Name",
      "sku": "SKU"
    },
    "form": {
      "title": "Create Product"
    }
  }
}
```

## Inertia.js Patterns

### Navigation

```js
import { router, Link } from '@inertiajs/vue3'

// Programmatic navigation
router.visit('/admin/products')
router.get('/admin/products', { search: 'query' })
router.post('/admin/products', formData)

// Link component
<Link href="/admin/products">Products</Link>
```

### Form Handling

```js
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  name: '',
  email: ''
})

form.post('/admin/users', {
  preserveScroll: true,
  onSuccess: () => {
    toast.success('User created')
  },
  onError: (errors) => {
    // Errors automatically in form.errors
  }
})

// Access form state
form.processing  // Boolean
form.errors      // Object
form.hasErrors   // Boolean
```

### Accessing Props

```js
import { usePage } from '@inertiajs/vue3'

const page = usePage()

// Access props
const user = computed(() => page.props.auth?.user)
const products = computed(() => page.props.products || [])
const flash = computed(() => page.props.flash)
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

## RTL/LTR Support

The dashboard automatically detects language direction from Laravel's locale:
- Arabic (`ar`) → RTL
- All other locales → LTR

To toggle direction at runtime:
```js
const appStore = useAppStore()
appStore.setDirection('rtl') // or 'ltr'
```

When writing components, use Tailwind's logical properties:
- ✅ `start`/`end` instead of `left`/`right`
- ✅ `ms`/`me` instead of `ml`/`mr`
- ✅ `ps`/`pe` instead of `pl`/`pr`

## Building for Production

```bash
# Build assets
npm run build

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Troubleshooting

**Vite not compiling:**
- Delete `node_modules` and run `npm install`
- Clear Vite cache: `rm -rf node_modules/.vite`

**Vue components not rendering:**
- Check browser console for errors
- Verify `@inertia` directive in blade template
- Ensure dev server is running

**Forms not submitting:**
- Check CSRF token in meta tag
- Verify routes in `routes/web.php`
- Check browser network tab for error details
- Ensure `failedValidation()` is overridden in FormRequest

**Props not available:**
- Verify controller returns `Inertia::render()`
- Check `HandleInertiaRequests` middleware is registered
- Ensure props are passed in controller

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

## Next Steps

1. Customize the design and colors in `tailwind.config.js`
2. Create more modules following the Products example
3. Add real backend controllers and models
4. Set up production deployment

For more information, see `CLAUDE.md` for detailed documentation.
