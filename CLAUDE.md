# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 12 + Vue 3 SaaS Dashboard application using:
- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Vue 3 (Composition API) + Vite + Tailwind CSS 4.0
- **State Management**: Pinia
- **Routing**: Vue Router
- **API Client**: Axios with interceptors
- **Database**: SQLite (default)
- **Queue**: Database-driven queue system
- **Testing**: PHPUnit
- **Animations**: @vueuse/motion
- **Date Picker**: Flatpickr
- **RTL/LTR Support**: tailwindcss-rtl

## Development Commands

### Starting Development Environment
```bash
composer dev
```
This runs a concurrent process that starts:
- PHP development server (`php artisan serve`)
- Queue listener (`php artisan queue:listen --tries=1`)
- Log viewer (`php artisan pail --timeout=0`)
- Vite dev server (`npm run dev`)

### Individual Services
```bash
# Start PHP server only
php artisan serve

# Start Vite dev server only
npm run dev

# Listen to queue jobs
php artisan queue:listen --tries=1

# View logs
php artisan pail
```

### Testing
```bash
# Run all tests
composer test

# Run PHPUnit directly
php artisan test

# Run specific test file
php artisan test tests/Feature/ExampleTest.php

# Run specific test method
php artisan test --filter test_method_name
```

### Building for Production
```bash
# Build frontend assets
npm run build
```

### Code Quality
```bash
# Format code with Laravel Pint
vendor/bin/pint

# Run Pint on specific file
vendor/bin/pint path/to/file.php
```

## Project Structure

### Application Layer
- `app/Http/Controllers/` - HTTP controllers
- `app/Models/` - Eloquent models (User model included by default)
- `app/Providers/` - Service providers (AppServiceProvider)

### Routes
- `routes/web.php` - Web routes
- `routes/console.php` - Artisan console commands

### Views & Assets
- `resources/views/` - Blade templates (welcome.blade.php is the default)
- `resources/css/app.css` - Main CSS entry point (Tailwind)
- `resources/js/app.js` - Main JavaScript entry point

### Database
- `database/migrations/` - Database migrations (users, cache, jobs tables included)
- `database/factories/` - Model factories for testing/seeding
- `database/seeders/` - Database seeders
- `database/database.sqlite` - SQLite database file

### Testing
- `tests/Feature/` - Feature tests
- `tests/Unit/` - Unit tests
- Test environment uses in-memory SQLite database

## Configuration

### Environment
- Uses SQLite database by default
- Database queue connection
- Log mailer for development
- Session stored in database

### Vite Configuration
Entry points are configured in `vite.config.js`:
- `resources/css/app.css`
- `resources/js/main.js` (Vue app entry point)

Hot module replacement is enabled for development.
Path alias `@` is configured to point to `resources/js/`.

## Common Laravel Artisan Commands

```bash
# Generate new controller
php artisan make:controller ControllerName

# Generate new model with migration
php artisan make:model ModelName -m

# Generate new migration
php artisan make:migration create_table_name

# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Interactive tinker shell
php artisan tinker

# Generate application key (if needed)
php artisan key:generate
```

## Vue 3 Dashboard Architecture

### Frontend Structure

```
resources/js/
├── main.js                    # Vue app entry point
├── router/
│   └── index.js              # Vue Router configuration
├── store/
│   ├── index.js              # App & Toast stores
│   └── users.js              # Users module store
├── services/
│   ├── api.js                # Axios wrapper with interceptors
│   └── users.js              # Users API service
├── composables/
│   ├── useFetch.js           # Fetch data composable
│   ├── useForm.js            # Form handling with validation
│   └── useImageUpload.js     # Image upload with preview
├── components/
│   ├── inputs/               # Form input components
│   │   ├── TextInput.vue
│   │   ├── Textarea.vue
│   │   ├── NumberInput.vue
│   │   ├── DatePicker.vue
│   │   ├── Select.vue
│   │   ├── MultiSelect.vue
│   │   ├── FileInput.vue
│   │   └── ImagePicker.vue
│   ├── tables/
│   │   └── DataTable.vue     # Server-side table with pagination
│   ├── layout/
│   │   ├── Sidebar.vue
│   │   ├── Topbar.vue
│   │   └── AppLayout.vue
│   └── ui/
│       ├── Button.vue
│       ├── Modal.vue
│       ├── Toast.vue
│       └── Spinner.vue
├── pages/
│   ├── Dashboard.vue
│   └── Modules/
│       └── Users/
│           ├── UsersIndex.vue
│           └── UsersForm.vue
└── utils/
    └── helpers.js            # Utility functions
```

### Component Usage

#### Input Components
All input components support:
- `v-model` for two-way binding
- `label`, `placeholder`, `error`, `hint` props
- `required`, `disabled`, `readonly` states

```vue
<TextInput v-model="form.name" label="Name" :error="errors.name" required />
<Select v-model="form.role" label="Role" :options="roleOptions" />
<DatePicker v-model="form.date" label="Date" dateFormat="Y-m-d" />
<ImagePicker v-model="form.avatar" label="Avatar" upload-url="/api/upload" />
```

#### DataTable Component
Server-side table with built-in features:

```vue
<DataTable
  :columns="columns"
  :data="items"
  :meta="meta"
  :loading="loading"
  @search="handleSearch"
  @sort="handleSort"
  @page-change="handlePageChange"
>
  <!-- Custom cell rendering -->
  <template #cell-status="{ row }">
    <span>{{ row.status }}</span>
  </template>

  <!-- Actions column -->
  <template #actions="{ row }">
    <button @click="edit(row)">Edit</button>
  </template>
</DataTable>
```

#### UI Components

```vue
<!-- Button with variants and loading state -->
<Button variant="primary" :loading="loading" @click="submit">Save</Button>

<!-- Modal with header and footer slots -->
<Modal v-model="showModal" title="Confirm Action">
  <p>Modal content</p>
  <template #footer>
    <Button @click="confirm">Confirm</Button>
  </template>
</Modal>

<!-- Spinner -->
<Spinner size="lg" color="primary" text="Loading..." />
```

### Composables

#### useForm
Form handling with validation and error management:

```js
const { form, errors, loading, getError, post, put } = useForm({
  name: '',
  email: ''
})

// Submit form
await post('/api/users', {
  successMessage: 'User created',
  onSuccess: (response) => router.push('/users')
})
```

#### useFetch
Fetch data with loading state:

```js
const { data, loading, error, execute, refresh } = useFetch('/api/users')
```

#### useImageUpload
Image upload with preview and progress:

```js
const { preview, uploading, progress, handleFileSelect, upload } = useImageUpload()
```

### State Management (Pinia)

#### Toast Store
```js
import { useToastStore } from '@/store'

const toast = useToastStore()
toast.success('Operation successful')
toast.error('Operation failed')
toast.warning('Warning message')
toast.info('Info message')
```

#### App Store
```js
import { useAppStore } from '@/store'

const appStore = useAppStore()
appStore.toggleSidebar()
appStore.setDirection('rtl') // or 'ltr'
```

#### Module Stores
Each module has its own Pinia store with standard actions:

```js
import { useUsersStore } from '@/store/users'

const usersStore = useUsersStore()
await usersStore.fetchList({ page: 1, search: 'query' })
await usersStore.fetchOne(id)
await usersStore.create(data)
await usersStore.update(id, data)
await usersStore.delete(id)
```

### API Service

The API service (`services/api.js`) provides:
- Automatic CSRF token handling
- Bearer token authentication
- Error normalization
- Request/response interceptors

```js
import api from '@/services/api'

const data = await api.get('/users', { page: 1 })
await api.post('/users', userData)
await api.put('/users/1', userData)
await api.delete('/users/1')
await api.upload('/upload', formData, onProgress)
```

### RTL/LTR Support

The dashboard automatically detects direction from Laravel locale:
- Arabic (`ar`) → RTL
- All others → LTR

Toggle direction at runtime:
```js
const appStore = useAppStore()
appStore.setDirection('rtl') // or 'ltr'
```

All components are RTL-ready using Tailwind's logical properties:
- Use `start`/`end` instead of `left`/`right`
- Use `ms`/`me` instead of `ml`/`mr`

### Creating New Modules

To add a new module (e.g., "Products"):

1. Create service: `resources/js/services/products.js`
2. Create store: `resources/js/store/products.js`
3. Create pages: `resources/js/pages/Modules/Products/`
4. Add routes in `resources/js/router/index.js`
5. Add menu item in `resources/js/components/layout/Sidebar.vue`
6. Create API endpoints in `routes/api.php`

### Utility Functions

Available in `resources/js/utils/helpers.js`:
- `formatDate(date, format)` - Format dates
- `formatCurrency(amount)` - Format currency
- `formatFileSize(bytes)` - Format file sizes
- `truncate(text, length)` - Truncate text
- `debounce(func, wait)` - Debounce function
- `capitalize(str)` - Capitalize string
- `getInitials(name)` - Get name initials
- And more...

## Architecture Notes

- This is a Laravel 12 + Vue 3 SPA application
- Backend serves a single blade template (`resources/views/app.blade.php`)
- All routing is handled by Vue Router on the frontend
- API endpoints in `routes/api.php` provide data to Vue components
- Frontend uses Vite for asset bundling with Tailwind CSS 4.0
- Queue jobs are processed via database driver (requires queue listener to be running)
- The concurrent dev command (`composer dev`) is the recommended way to run the full stack during development
