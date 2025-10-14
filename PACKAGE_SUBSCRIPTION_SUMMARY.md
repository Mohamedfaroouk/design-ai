# Package & Subscription System - Implementation Summary

## ✅ Completed Implementation

### Database Schema

#### **packages table**
- `name` (unique) - Used by Salla to match packages
- `display_name` - Display name in UI
- `price`, `currency` - Package pricing
- `billing_cycle` - monthly, yearly, lifetime
- `photos_limit` - Number of photos allowed (0 = unlimited)
- `platform` - salla, zid, wordpress, or 'all'
- `features`, `metadata` - JSON fields for additional data
- `is_active`, `is_featured`, `sort_order` - Display controls

#### **subscriptions table**
- `user_id` + `platform` (unique) - One subscription per user per platform
- `package_id` - Current package
- `package_data` - Full package snapshot (JSON)
- `photos_limit`, `photos_used` - Photo tracking
- `status` - active, trial, cancelled, expired, suspended
- `start_date`, `end_date`, `trial_ends_at` - Subscription period
- `merchant_id`, `subscription_id` - Platform identifiers
- `metadata` - Additional platform-specific data

#### **subscription_histories table**
- Tracks all subscription events
- `event_type` - started, renewed, upgraded, downgraded, cancelled, expired, trial_started, trial_expired
- `package_data` - Package data at time of event
- `changes` - What changed (old vs new package)
- `webhook_payload` - Full Salla webhook data
- `price`, `start_date`, `end_date` - Event details

### Models

✅ **Package** (`app/Models/Package.php`)
- `findByName()` - Find package by name (used by Salla)
- `isAvailableFor()` - Check platform compatibility
- `subscriptions()` - HasMany relationship
- Scopes: `active()`, `forPlatform()`, `featured()`
- Casts: `billing_cycle` to BillingCycle enum, `features` and `metadata` to arrays

✅ **Subscription** (`app/Models/Subscription.php`)
- `canUsePhotos(int $count)` - Check if user can use photos
- `usePhotos(int $count)` - Use photos and increment counter
- `remainingPhotos()` - Get remaining photos count
- `isActive()`, `isExpired()`, `isTrial()` - Status checkers
- `user()`, `package()`, `histories()` - Relationships
- Scopes: `active()`, `forPlatform()`, `expired()`

✅ **SubscriptionHistory** (`app/Models/SubscriptionHistory.php`)
- `subscription()`, `user()`, `package()` - Relationships
- Scopes: `event()`, `forPlatform()`

✅ **User** (`app/Models/User.php`)
- `subscriptions()` - HasMany relationship
- `activeSubscription(string $platform)` - Get active subscription for platform
- `canUsePhotos(string $platform, int $count)` - Check photo usage
- `subscriptionHistories()` - HasMany relationship

### Enums

✅ **SubscriptionStatus** (`app/Enums/SubscriptionStatus.php`)
- ACTIVE, TRIAL, CANCELLED, EXPIRED, SUSPENDED
- `isActive()` method - Returns true for ACTIVE and TRIAL

✅ **BillingCycle** (`app/Enums/BillingCycle.php`)
- MONTHLY, YEARLY, LIFETIME
- `months()` method - Returns duration in months

### Services

✅ **SallaSubscriptionService** (`app/Services/Store/Salla/SallaSubscriptionService.php`)
- `handleSubscriptionStarted(array $data)` - Creates/updates subscription when Salla sends subscription.started
- `handleSubscriptionRenewed(array $data)` - Updates subscription on renewal
- `handleSubscriptionExpired(array $data)` - Marks subscription as expired
- `handleTrialStarted(array $data)` - Handles free trial activation
- `handleTrialExpired(array $data)` - Handles trial expiration
- `findUserByMerchant(string $merchantId)` - Finds user via stores table
- `findPackageByPlanName(string $planName, PlatformType $platform)` - Matches package by name
- `createHistory()` - Creates subscription history record

✅ **SallaWebhookService** (`app/Services/Store/Salla/SallaWebhookService.php`)
- `handleSubscriptionStarted(array $data)` - Routes to SallaSubscriptionService
- `handleSubscriptionRenewed(array $data)` - Routes to SallaSubscriptionService
- `handleSubscriptionExpired(array $data)` - Routes to SallaSubscriptionService
- `handleTrialStarted(array $data)` - Routes to SallaSubscriptionService
- `handleTrialExpired(array $data)` - Routes to SallaSubscriptionService
- All methods log webhooks and handle errors with proper logging

### Controllers

✅ **PackageController** (`app/Http/Controllers/Admin/PackageController.php`)
- Uses `HasDataTable` trait for index with search/sort/pagination
- `index()` - List packages with filters (platform, billing_cycle, is_active, is_featured)
- `list()` - Get active packages for dropdown (no pagination)
- `store()` - Create new package
- `show()` - Get single package
- `update()` - Update package
- `destroy()` - Delete package (checks for active subscriptions first)

✅ **SallaWebhookController** (`app/Http/Controllers/Integration/SallaWebhookController.php`)
- Routes subscription webhooks to SallaWebhookService handlers:
  - subscription.started
  - subscription.renewed
  - subscription.expired
  - trial.started
  - trial.expired

### Requests

✅ **StorePackageRequest** (`app/Http/Requests/Admin/StorePackageRequest.php`)
- Validates: name (unique), display_name, platform, price, currency, billing_cycle, photos_limit
- Authorization: checks `packages.create` permission
- Custom validation messages

✅ **UpdatePackageRequest** (`app/Http/Requests/Admin/UpdatePackageRequest.php`)
- Same validation as StorePackageRequest but with `sometimes` for partial updates
- Uses `Rule::unique()->ignore($packageId)` for name uniqueness
- Authorization: checks `packages.update` permission

### Resources

✅ **PackageResource** (`app/Http/Resources/Admin/PackageResource.php`)
- Formats package data for API responses
- Includes `formatted_price` (currency + formatted number)
- Includes `photos_limit_text` ("Unlimited" or formatted number)
- Returns all package fields with proper formatting

### Routes

✅ **Admin Routes** (`routes/api.php`)
```php
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::prefix('packages')->group(function () {
        Route::get('/', [PackageController::class, 'index'])->middleware('permission:packages.view');
        Route::get('/list', [PackageController::class, 'list'])->middleware('permission:packages.view');
        Route::post('/', [PackageController::class, 'store'])->middleware('permission:packages.create');
        Route::get('/{id}', [PackageController::class, 'show'])->middleware('permission:packages.view');
        Route::put('/{id}', [PackageController::class, 'update'])->middleware('permission:packages.edit');
        Route::delete('/{id}', [PackageController::class, 'destroy'])->middleware('permission:packages.delete');
    });
});
```

✅ **Public Routes** (`routes/api.php`)
```php
// Public packages endpoint (for pricing page)
Route::get('/packages', [PackageController::class, 'list']);
Route::get('/packages/platform/{platform}', [PackageController::class, 'list']);
```

✅ **Webhook Routes** (already existed, updated)
```php
// Salla Webhooks - handles subscription events
Route::post('/webhooks/salla', [SallaWebhookController::class, 'handle']);
```

### Translations

✅ **English** (`lang/en/packages.php` & `lang/en/subscriptions.php`)
- Package fields, billing cycles, platforms
- Subscription fields, statuses, event types
- Success/error messages
- History-related translations

✅ **Arabic** (`lang/ar/packages.php` & `lang/ar/subscriptions.php`)
- Complete Arabic translations for all package and subscription text
- Proper RTL-ready translations

### Seeders

✅ **PackagesSeeder** (`database/seeders/PackagesSeeder.php`)
- Creates example packages:
  - **Salla packages**: Basic (100 photos, 99 SAR), Professional (500 photos, 299 SAR), Enterprise (unlimited, 799 SAR)
  - **Free trial**: 10 photos, all platforms
  - **Zid packages**: Placeholder for future (inactive)
  - **WordPress packages**: Placeholder for future (inactive)
- Includes features, metadata, sort_order
- Featured/recommended flags
- Already run and data populated

✅ **DatabaseSeeder** updated to include PackagesSeeder

## 🎯 Key Business Rules Implemented

1. ✅ **One subscription per user per platform** - Enforced by unique constraint on `user_id` + `platform`
2. ✅ **Package matching by name** - Salla sends `plan_name`, matched against `packages.name` field
3. ✅ **Update existing subscription, create history** - When subscription already exists, it's updated but history is always created
4. ✅ **Full package snapshot** - `package_data` JSON field stores complete package details at subscription time
5. ✅ **Photo usage tracking** - Separate `photos_limit` and `photos_used` with helper methods
6. ✅ **History for all events** - Every subscription change creates a history record with webhook payload
7. ✅ **Event type determination** - System detects if it's a new subscription or upgrade
8. ✅ **Reset photos on new subscription** - `photos_used` reset to 0 only for new subscriptions, preserved for upgrades
9. ✅ **Unlimited photos** - `photos_limit = 0` means unlimited

## 📊 Salla Integration Flow

### When Salla Sends subscription.started Webhook:

1. **Webhook received** → `/api/webhooks/salla`
2. **Logged** → `webhook_logs` table (pending status)
3. **Routed** → SallaWebhookController → SallaWebhookService → SallaSubscriptionService
4. **User found** → Via `merchant_id` from stores table
5. **Package found** → By matching `plan_name` to `packages.name`
6. **Check existing** → Query `subscriptions` for user + platform
7. **If exists**:
   - Update subscription with new package
   - Keep existing `photos_used`
   - Create history record with `event_type='upgraded'`
8. **If not exists**:
   - Create new subscription
   - Set `photos_used=0`
   - Create history record with `event_type='started'`
9. **Webhook marked** → `processed` status in webhook_logs
10. **Logged** → Info log with subscription_id and user_id

### Webhook Events Handled:
- ✅ `subscription.started` - New subscription or upgrade
- ✅ `subscription.renewed` - Subscription renewal
- ✅ `subscription.expired` - Subscription expired
- ✅ `trial.started` - Free trial started
- ✅ `trial.expired` - Free trial expired

## 📸 Photo Usage Example

```php
// When user wants to process photos:
$user = auth()->user();
$platform = 'salla';

// Check if user can use photos
if ($user->canUsePhotos($platform, 5)) {
    // Get subscription
    $subscription = $user->activeSubscription($platform);

    // Use photos (increments photos_used)
    $subscription->usePhotos(5);

    // Process photos...

    // Check remaining
    $remaining = $subscription->remainingPhotos();
    // Returns int (or PHP_INT_MAX if unlimited)
} else {
    // Show error: "Photo limit exceeded"
}
```

## 🔐 Permissions Required

The following permissions need to be created and assigned to roles:

```php
// Packages permissions
'packages.view'
'packages.create'
'packages.edit'
'packages.delete'
```

**Note**: These permissions should be added to the RolesSeeder or created via admin panel.

## 📝 API Endpoints Summary

### Admin Endpoints (Authenticated + Permission)
- `GET /api/admin/packages` - List packages (pagination, search, filters)
- `GET /api/admin/packages/list` - Dropdown list (active packages only)
- `POST /api/admin/packages` - Create package
- `GET /api/admin/packages/{id}` - Get single package
- `PUT /api/admin/packages/{id}` - Update package
- `DELETE /api/admin/packages/{id}` - Delete package (checks active subscriptions)

### Public Endpoints
- `GET /api/packages` - List active packages (for pricing page)
- `GET /api/packages/platform/{platform}` - List active packages for specific platform

### Webhook Endpoints
- `POST /api/webhooks/salla` - Receive Salla webhooks (handles subscription events)

## 🎉 Implementation Complete!

All core functionality for the package and subscription system has been implemented:

- ✅ Database migrations run and tables created
- ✅ Models with relationships and helper methods
- ✅ Enums for status and billing cycles
- ✅ Service layer for business logic (SallaSubscriptionService)
- ✅ Webhook integration (SallaWebhookService updated)
- ✅ Admin API endpoints with permissions
- ✅ Public API endpoints
- ✅ Validation (Request classes)
- ✅ API Resources for response formatting
- ✅ Translations (EN/AR)
- ✅ Seeder with example packages
- ✅ Data populated in database

## 📚 Next Steps (Optional Enhancements)

1. **Subscription Management APIs** (Client-facing):
   - `GET /api/client/subscription` - Get my current subscriptions
   - `GET /api/client/subscription/history` - Get my subscription history
   - `POST /api/client/subscription/use-photos` - Use photos endpoint

2. **Admin Subscription Management**:
   - `GET /api/admin/subscriptions` - List all subscriptions
   - `GET /api/admin/subscriptions/{id}` - Get subscription details
   - `GET /api/admin/subscriptions/{id}/history` - Get subscription history
   - `PUT /api/admin/subscriptions/{id}` - Update subscription (manual override)

3. **Permissions Setup**:
   - Add `packages.*` permissions to RolesSeeder
   - Assign to admin role

4. **Frontend Implementation**:
   - Admin: Package management UI (CRUD)
   - Client: View current subscription and usage
   - Public: Pricing page showing available packages

5. **Testing**:
   - Unit tests for SubscriptionService
   - Feature tests for webhook handling
   - API tests for package endpoints

## 🔗 Related Documentation

- See `STORE_INTEGRATION.md` for store connection details
- See `CLAUDE.md` for overall project architecture
- See Salla docs: https://docs.salla.dev/421118m0 for webhook details
