<?php

namespace App\Models;

use App\Enums\PlatformType;
use App\Enums\StoreStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    protected $fillable = [
        'user_id',
        'platform',
        'merchant_id',
        'store_id',
        'domain',
        'store_name',
        'store_email',
        'store_phone',
        'avatar',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'status',
        'metadata',
    ];

    protected $casts = [
        'platform' => PlatformType::class,
        'status' => StoreStatus::class,
        'metadata' => 'array',
        'token_expires_at' => 'datetime',
    ];

    protected $hidden = [
        'access_token',
        'refresh_token',
    ];

    /**
     * Get the user that owns the store
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get webhook logs for this store
     */
    public function webhookLogs(): HasMany
    {
        return $this->hasMany(WebhookLog::class);
    }

    /**
     * Check if token is expired or about to expire
     */
    public function isTokenExpired(): bool
    {
        if (!$this->token_expires_at) {
            return false;
        }

        return $this->token_expires_at->isPast();
    }

    /**
     * Check if token needs refresh (expires in less than 1 day)
     */
    public function needsTokenRefresh(): bool
    {
        if (!$this->token_expires_at) {
            return false;
        }

        return $this->token_expires_at->subDay()->isPast();
    }

    /**
     * Scope to get stores by platform
     */
    public function scopePlatform($query, PlatformType $platform)
    {
        return $query->where('platform', $platform);
    }

    /**
     * Scope to get active stores
     */
    public function scopeActive($query)
    {
        return $query->where('status', StoreStatus::ACTIVE);
    }

    /**
     * Scope to get stores needing token refresh
     */
    public function scopeNeedingRefresh($query)
    {
        return $query->where('token_expires_at', '<=', now()->addDay())
                     ->whereNotNull('refresh_token');
    }
}
