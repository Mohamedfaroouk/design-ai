# Store Integration System

This document explains the multi-platform store integration system that supports **Salla**, **Zid**, and **WordPress/WooCommerce**.

## 📋 Overview

The system is designed with a flexible architecture that allows easy addition of new e-commerce platforms. Currently, **Salla** is fully implemented, while **Zid** and **WordPress** are set up with placeholder implementations.

### Key Features

- ✅ **Multi-platform support**: Salla (ready), Zid (placeholder), WordPress (placeholder)
- ✅ **OAuth 2.0 authentication** for secure store authorization
- ✅ **Webhook handling** for real-time store events
- ✅ **Token refresh mechanism** to maintain access
- ✅ **User deduplication** via email (no duplicate users)
- ✅ **Flexible architecture** for easy platform additions
- ✅ **Comprehensive logging** with webhook history
- ✅ **Admin panel** for store management

## 🏗️ Architecture

### Database Schema

**stores table:**
- `user_id` - Foreign key to users
- `platform` - Platform type (salla, zid, wordpress)
- `merchant_id` - Unique merchant identifier
- `store_id`, `domain`, `store_name` - Store details
- `access_token`, `refresh_token` - OAuth tokens
- `token_expires_at` - Token expiration timestamp
- `status` - Store status (active, inactive, suspended)
- `metadata` - JSON field for platform-specific data

**webhook_logs table:**
- `event` - Webhook event type
- `platform` - Platform that sent the webhook
- `merchant_id` - Merchant identifier
- `user_id`, `store_id` - Related user and store
- `payload` - Complete webhook payload
- `status` - Processing status (pending, processed, failed)

### Service Architecture

```
app/Services/Store/
├── Contracts/
│   ├── StoreAuthInterface.php      # OAuth authentication contract
│   └── StoreWebhookInterface.php   # Webhook handling contract
├── Salla/
│   ├── SallaAuthService.php        # Salla OAuth implementation
│   └── SallaWebhookService.php     # Salla webhook handlers
├── Zid/
│   ├── ZidAuthService.php          # Zid OAuth (placeholder)
│   └── ZidWebhookService.php       # Zid webhooks (placeholder)
└── WordPress/
    ├── WordPressAuthService.php    # WordPress OAuth (placeholder)
    └── WordPressWebhookService.php # WordPress webhooks (placeholder)
```

## 🚀 Setup

### 1. Environment Configuration

Add your platform credentials to `.env`:

```env
# Salla
SALLA_CLIENT_ID=your_client_id
SALLA_CLIENT_SECRET=your_client_secret
SALLA_REDIRECT_URI=https://yourdomain.com/api/integration/salla/callback

# Zid (when implemented)
ZID_CLIENT_ID=
ZID_CLIENT_SECRET=
ZID_REDIRECT_URI=

# WordPress (when implemented)
WORDPRESS_CLIENT_ID=
WORDPRESS_CLIENT_SECRET=
WORDPRESS_REDIRECT_URI=
```

### 2. Run Migrations

```bash
php artisan migrate
```

This creates the `stores` and `webhook_logs` tables.

### 3. Configure Permissions

Add store management permissions to your roles seeder:

```php
Permission::create(['name' => 'stores.view']);
Permission::create(['name' => 'stores.edit']);
Permission::create(['name' => 'stores.delete']);
```

### 4. Configure Webhooks (Salla)

In your Salla Partner Portal:
1. Go to your app settings
2. Set webhook URL: `https://yourdomain.com/api/webhooks/salla`
3. Subscribe to events:
   - `app.store.authorize`
   - `app.installed`
   - `app.uninstalled`

## 📡 API Endpoints

### Store Management (Admin)

```
GET    /api/admin/stores              # List all stores (with filters)
GET    /api/admin/stores/{id}         # Get store details
PUT    /api/admin/stores/{id}         # Update store
DELETE /api/admin/stores/{id}         # Delete store
POST   /api/admin/stores/{id}/refresh-token  # Refresh access token
```

**Permissions required**: `stores.view`, `stores.edit`, `stores.delete`

### Salla OAuth

```
GET  /api/integration/salla/authorize  # Get authorization URL
GET  /api/integration/salla/callback   # OAuth callback handler
```

### Salla Webhooks

```
POST /api/webhooks/salla  # Receive Salla webhooks
```

## 🔄 OAuth Flow

### Salla Authorization Flow

1. **Get Authorization URL**
   ```bash
   GET /api/integration/salla/authorize
   ```
   Response:
   ```json
   {
     "authorization_url": "https://accounts.salla.sa/oauth2/authorize?..."
   }
   ```

2. **User authorizes the app** on Salla

3. **Salla redirects** to callback with authorization code

4. **Callback processes authorization**
   ```bash
   GET /api/integration/salla/callback?code=AUTH_CODE
   ```
   Response:
   ```json
   {
     "message": "Salla store authorized successfully",
     "data": {
       "user": {...},
       "store": {...},
       "token": "sanctum_api_token",
       "reset_url": "https://yourdomain.com/reset-password?token=..."
     }
   }
   ```

## 👥 User Management

### Email-Based Deduplication

The system **prevents duplicate users** by checking email addresses:

```php
// Check if user exists by email
$user = User::where('email', $userData['email'])->first();

if (!$user) {
    // Create new user
    $user = $this->createUser($userData);
}

// Create new store for existing or new user
$this->createStore($user, $userData, $tokenData);
```

**Scenarios:**

1. **New user + New store**: Creates both user and store
2. **Existing user (by email) + New store**: Adds store to existing user
3. **Existing store (by merchant_id)**: Updates store and user details

## 🪝 Webhook Events

### Salla Webhook Events

| Event | Description | Handler |
|-------|-------------|---------|
| `app.store.authorize` | OAuth token granted | `handleStoreAuthorized()` |
| `app.installed` | App installed/activated | `handleStoreInstalled()` |
| `app.uninstalled` | App uninstalled/deactivated | `handleStoreUninstalled()` |

### Webhook Processing

All webhooks are:
1. **Logged** to `webhook_logs` table
2. **Validated** for authenticity
3. **Processed** by appropriate handler
4. **Marked** as processed/failed with error details

## 🔧 Adding New Platforms

To add support for **Zid** or **WordPress**:

### 1. Implement Auth Service

```php
// app/Services/Store/Zid/ZidAuthService.php

class ZidAuthService implements StoreAuthInterface
{
    public function getAccessToken(array $data): array
    {
        // Implement Zid OAuth token exchange
    }

    public function getUserInfo(string $accessToken): array
    {
        // Fetch user/store info from Zid API
    }

    public function refreshToken(Store $store): bool
    {
        // Implement token refresh logic
    }

    // ... other methods
}
```

### 2. Implement Webhook Service

```php
// app/Services/Store/Zid/ZidWebhookService.php

class ZidWebhookService implements StoreWebhookInterface
{
    public function handleStoreAuthorized(array $data): void
    {
        // Handle Zid authorization webhook
    }

    public function validateWebhook(array $headers, string $payload): bool
    {
        // Validate Zid webhook signature
    }

    // ... other methods
}
```

### 3. Create Controllers

```php
// app/Http/Controllers/Integration/ZidOAuthController.php
// app/Http/Controllers/Integration/ZidWebhookController.php
```

### 4. Add Routes

```php
// routes/api.php

Route::prefix('integration/zid')->group(function () {
    Route::get('/authorize', [ZidOAuthController::class, 'redirect']);
    Route::get('/callback', [ZidOAuthController::class, 'callback']);
});

Route::post('/webhooks/zid', [ZidWebhookController::class, 'handle']);
```

### 5. Update Service Resolution

```php
// app/Services/Admin/StoreService.php

public function refreshStoreToken(int $id): Store
{
    $store = Store::findOrFail($id);

    $authService = match ($store->platform->value) {
        'salla' => app(\App\Services\Store\Salla\SallaAuthService::class),
        'zid' => app(\App\Services\Store\Zid\ZidAuthService::class),
        'wordpress' => app(\App\Services\Store\WordPress\WordPressAuthService::class),
    };

    $authService->refreshToken($store);
    return $store->fresh();
}
```

## ⏰ Automatic Token Refresh

### Scheduled Command

The system automatically refreshes expiring tokens **every 6 hours** using Laravel's task scheduler.

**Command:**
```bash
php artisan stores:refresh-tokens
```

**Options:**
```bash
# Refresh only Salla stores
php artisan stores:refresh-tokens --platform=salla

# Force refresh all tokens (ignore expiration check)
php artisan stores:refresh-tokens --force

# Refresh only Zid stores (when implemented)
php artisan stores:refresh-tokens --platform=zid
```

**Schedule Configuration:**
```php
// bootstrap/app.php
$schedule->command('stores:refresh-tokens')
         ->everySixHours()
         ->withoutOverlapping()
         ->runInBackground();
```

**To Run Scheduler:**

In production, add this to your crontab:
```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

For local development:
```bash
php artisan schedule:work
```

**View Scheduled Tasks:**
```bash
php artisan schedule:list
```

### How It Works

1. **Auto-detect expiring tokens**: Checks `token_expires_at` < 24 hours
2. **Refresh tokens**: Calls platform-specific auth service
3. **Update database**: Stores new access_token, refresh_token, expires_at
4. **Logging**: Success/failure logged to `storage/logs/laravel.log`
5. **Progress bar**: Shows real-time progress during refresh

### Manual Token Refresh

Refresh a specific store via API:
```bash
POST /api/admin/stores/{id}/refresh-token
```

Or via admin panel (when frontend is built).

## 🔒 Security

### Token Management

- **Access tokens** are encrypted in database
- **Refresh tokens** enable automatic token renewal
- **Token expiration** is tracked with `token_expires_at`
- **Auto-refresh** when token expires in < 24 hours

### Webhook Validation

Implement signature validation in each webhook service:

```php
public function validateWebhook(array $headers, string $payload): bool
{
    // Verify webhook signature/authorization
    $signature = $headers['authorization'] ?? '';
    $secret = config('services.salla.client_secret');

    return hash_equals(
        hash_hmac('sha256', $payload, $secret),
        $signature
    );
}
```

## 📊 Database Queries

### Get stores needing token refresh

```php
$stores = Store::needingRefresh()->get();

foreach ($stores as $store) {
    $authService->refreshToken($store);
}
```

### Get active stores by platform

```php
$sallaStores = Store::platform(PlatformType::SALLA)
                    ->active()
                    ->get();
```

### Get webhook logs for a store

```php
$logs = WebhookLog::where('store_id', $storeId)
                  ->latest()
                  ->paginate(20);
```

## 🧪 Testing

### Test OAuth Flow

```bash
# 1. Get authorization URL
curl -X GET http://localhost/api/integration/salla/authorize

# 2. Visit URL, authorize app, copy code from redirect

# 3. Complete authorization
curl -X GET "http://localhost/api/integration/salla/callback?code=AUTH_CODE"
```

### Test Webhooks

```bash
curl -X POST http://localhost/api/webhooks/salla \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer test_signature" \
  -d '{
    "event": "app.installed",
    "merchant": "123456789",
    "created_at": "2024-01-01T00:00:00Z"
  }'
```

## 📝 TODO

- [ ] Implement Zid OAuth and webhooks
- [ ] Implement WordPress/WooCommerce OAuth and webhooks
- [x] ✅ Add automatic token refresh command (scheduler)
- [ ] Add webhook retry mechanism
- [ ] Add store sync functionality
- [ ] Create frontend UI for store management

## 🔗 Resources

- [Salla API Documentation](https://docs.salla.dev/)
- [Zid API Documentation](https://docs.zid.sa/)
- [WooCommerce REST API](https://woocommerce.github.io/woocommerce-rest-api-docs/)

## 📞 Support

For issues or questions about the store integration system, please check:
- Backend logs: `storage/logs/laravel.log`
- Webhook logs: `webhook_logs` table
- Store details: `stores` table
