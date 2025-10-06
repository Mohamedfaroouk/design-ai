# Vue 3 Dashboard - Quick Start Guide

## Overview

This Laravel + Vue 3 SaaS Dashboard includes:

✅ Complete Vue 3 setup with Composition API
✅ Pinia state management
✅ Vue Router with transitions
✅ 8 reusable input components
✅ DataTable with server-side pagination
✅ Complete Users CRUD module
✅ RTL/LTR support
✅ Toast notifications
✅ Modal dialogs
✅ Form validation
✅ Image upload with preview
✅ API service with interceptors

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
- `AppLayout.vue` - Main layout wrapper
- `Sidebar.vue` - Collapsible sidebar navigation
- `Topbar.vue` - Top navigation bar with user menu

**Table Components** (`resources/js/components/tables/`)
- `DataTable.vue` - Full-featured data table with:
  - Server-side pagination
  - Sorting
  - Search
  - Custom cell rendering
  - Actions column

### Composables

**useFetch** - Fetch data with loading state
```js
const { data, loading, error, refresh } = useFetch('/api/users')
```

**useForm** - Form handling with validation
```js
const { form, errors, loading, post, put } = useForm({ name: '', email: '' })
await post('/api/users', { successMessage: 'Created!' })
```

**useImageUpload** - Image upload with preview
```js
const { preview, uploading, progress, upload } = useImageUpload()
```

### State Management

**Toast Store** - Show notifications
```js
import { useToastStore } from '@/store'
const toast = useToastStore()

toast.success('Success message')
toast.error('Error message')
toast.warning('Warning message')
toast.info('Info message')
```

**App Store** - App-level state
```js
import { useAppStore } from '@/store'
const appStore = useAppStore()

appStore.toggleSidebar()
appStore.setDirection('rtl') // or 'ltr'
```

**Module Stores** - Feature-specific state
```js
import { useUsersStore } from '@/store/users'
const usersStore = useUsersStore()

await usersStore.fetchList()
await usersStore.create(data)
await usersStore.update(id, data)
await usersStore.delete(id)
```

## Creating a New Module

Follow this example to create a "Products" module:

### 1. Create API Service

`resources/js/services/products.js`
```js
import api from './api'

export default {
  async getProducts(params = {}) {
    return await api.get('/products', params)
  },
  async getProduct(id) {
    return await api.get(`/products/${id}`)
  },
  async createProduct(data) {
    return await api.post('/products', data)
  },
  async updateProduct(id, data) {
    return await api.put(`/products/${id}`, data)
  },
  async deleteProduct(id) {
    return await api.delete(`/products/${id}`)
  }
}
```

### 2. Create Pinia Store

`resources/js/store/products.js`
```js
import { defineStore } from 'pinia'
import productsService from '@/services/products'

export const useProductsStore = defineStore('products', {
  state: () => ({
    items: [],
    meta: null,
    loading: false
  }),

  actions: {
    async fetchList(params = {}) {
      this.loading = true
      try {
        const response = await productsService.getProducts(params)
        this.items = response.data
        this.meta = response.meta
      } finally {
        this.loading = false
      }
    }
    // Add other actions...
  }
})
```

### 3. Create Pages

Create `resources/js/pages/Modules/Products/ProductsIndex.vue` and `ProductsForm.vue` similar to the Users module.

### 4. Add Routes

In `resources/js/router/index.js`:
```js
{
  path: 'products',
  name: 'products.index',
  component: () => import('@/pages/Modules/Products/ProductsIndex.vue'),
  meta: { title: 'Products' }
},
{
  path: 'products/create',
  name: 'products.create',
  component: () => import('@/pages/Modules/Products/ProductsForm.vue'),
  meta: { title: 'Create Product' }
}
```

### 5. Add to Sidebar

In `resources/js/components/layout/Sidebar.vue`, add to `menuItems`:
```js
{
  name: 'products',
  label: 'Products',
  route: '/products',
  icon: () => h('svg', { /* icon SVG */ })
}
```

### 6. Create API Endpoints

Add routes in `routes/api.php`:
```php
Route::prefix('products')->group(function () {
  Route::apiResource('/', ProductController::class);
});
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

## API Response Format

All API endpoints should return data in this format:

**List Response:**
```json
{
  "data": [...],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 75,
    "from": 1,
    "to": 15
  }
}
```

**Single Resource:**
```json
{
  "data": { "id": 1, "name": "..." }
}
```

**Success/Error:**
```json
{
  "message": "Operation successful",
  "data": { ... }
}
```

**Validation Errors:**
```json
{
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "name": ["The name field is required."]
  }
}
```

## Utility Helpers

Available in `@/utils/helpers.js`:

```js
import { formatDate, formatCurrency, truncate, debounce } from '@/utils/helpers'

formatDate('2024-01-01', 'long') // January 1, 2024
formatCurrency(1234.56) // $1,234.56
truncate('Long text...', 10) // Long text...
debounce(searchFunction, 300) // Debounced function
```

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
- Verify `@vite` directive in blade template
- Ensure dev server is running

**API calls failing:**
- Check CSRF token in meta tag
- Verify API routes in `routes/api.php`
- Check browser network tab for error details

## Next Steps

1. Customize the design and colors in `tailwind.config.js`
2. Add authentication (Laravel Sanctum/Breeze)
3. Create more modules following the Users example
4. Add real backend controllers and models
5. Set up production deployment

For more information, see `CLAUDE.md` for detailed documentation.
