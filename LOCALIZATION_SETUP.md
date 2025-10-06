# Localization Setup

## Overview

This application supports **English (en)** and **Arabic (ar)** localization across both backend and frontend.

## Backend Setup

### 1. Locale Middleware

**File:** `app/Http/Middleware/SetLocale.php`

The middleware automatically detects and sets the locale based on:

**Priority Order:**
1. **Query parameter**: `?lang=ar`
2. **Accept-Language header**: Sent from frontend
3. **User preference**: `auth()->user()->locale` (if authenticated)
4. **Default**: `en`

**Features:**
- Validates locale against supported locales (`en`, `ar`)
- Sets `App::setLocale()` for all translations
- Returns `Content-Language` header in response

### 2. Registration

The middleware is registered in `bootstrap/app.php` for all API routes:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->api(append: [
        \App\Http\Middleware\SetLocale::class,
    ]);
})
```

### 3. Usage in Code

```php
// In controllers, services, or anywhere
return response()->json([
    'message' => __('auth.login.success')
]);

// Translation files location:
// lang/en/auth.php
// lang/ar/auth.php
```

## Frontend Setup

### 1. Accept-Language Header

**File:** `resources/js/services/api.js`

Every API request automatically includes the Accept-Language header:

```js
// Request interceptor
api.interceptors.request.use((config) => {
    const locale = localStorage.getItem('locale') || 'en'
    config.headers['Accept-Language'] = locale
    return config
})
```

### 2. i18n Configuration

**File:** `resources/js/i18n/index.js`

Vue i18n is configured to use localStorage:

```js
const i18n = createI18n({
    locale: localStorage.getItem('locale') || 'en',
    fallbackLocale: 'en',
    messages: { en, ar }
})
```

### 3. Language Switcher

**Component:** `resources/js/components/ui/LanguageSwitcher.vue`

The language switcher component:
- Saves locale to `localStorage`
- Updates `locale.value` (Vue i18n)
- Updates `direction` (RTL/LTR) in app store
- All subsequent API calls use the new locale

## Testing

### Backend Test

```bash
# Test with query parameter
curl http://localhost:8000/api/auth/login?lang=ar

# Test with Accept-Language header
curl -H "Accept-Language: ar" http://localhost:8000/api/auth/login

# Check response has Content-Language header
```

### Frontend Test

1. Open browser DevTools → Network tab
2. Click language switcher (🇬🇧 or 🇸🇦)
3. Check any API request headers:
   - Should see `Accept-Language: ar` or `Accept-Language: en`
4. Check response headers:
   - Should see `Content-Language: ar` or `Content-Language: en`

## Adding New Translations

### Backend

**1. Create translation file:**
```php
// lang/en/products.php
return [
    'created' => 'Product created successfully',
];

// lang/ar/products.php
return [
    'created' => 'تم إنشاء المنتج بنجاح',
];
```

**2. Use in code:**
```php
return response()->json([
    'message' => __('products.created')
]);
```

### Frontend

**1. Add to translation files:**
```json
// resources/js/i18n/locales/en.json
{
    "products": {
        "title": "Products",
        "created": "Product created"
    }
}

// resources/js/i18n/locales/ar.json
{
    "products": {
        "title": "المنتجات",
        "created": "تم إنشاء المنتج"
    }
}
```

**2. Use in components:**
```vue
<template>
    <h1>{{ $t('products.title') }}</h1>
</template>
```

## Supported Locales

- **English (en)** - Default
- **Arabic (ar)** - RTL support enabled

To add more locales:
1. Update `SetLocale::$supportedLocales` array
2. Create translation files in `lang/{locale}/`
3. Create frontend translations in `resources/js/i18n/locales/{locale}.json`
4. Update `LanguageSwitcher.vue` languages array

## Best Practices

✅ **Always use translation keys** - Never hardcode text
✅ **Consistent naming** - Use `module.action` pattern (e.g., `users.created`)
✅ **Both EN and AR** - Always provide translations for both languages
✅ **Test both locales** - Verify UI works in both English and Arabic
✅ **RTL support** - Use `ms`/`me` instead of `ml`/`mr` in Tailwind classes
