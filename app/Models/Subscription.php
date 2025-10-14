<?php

namespace App\Models;

use App\Enums\PlatformType;
use App\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'package_id',
        'platform',
        'merchant_id',
        'subscription_id',
        'status',
        'package_data',
        'photos_limit',
        'photos_used',
        'start_date',
        'end_date',
        'trial_ends_at',
        'cancelled_at',
        'metadata',
    ];

    protected $casts = [
        'platform' => PlatformType::class,
        'status' => SubscriptionStatus::class,
        'package_data' => 'array',
        'metadata' => 'array',
        'photos_limit' => 'integer',
        'photos_used' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'trial_ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Get the user that owns the subscription
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the package
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Get subscription histories
     */
    public function histories(): HasMany
    {
        return $this->hasMany(SubscriptionHistory::class);
    }

    /**
     * Check if user can use specified number of photos
     */
    public function canUsePhotos(int $count = 1): bool
    {
        if (!$this->status->isActive()) {
            return false;
        }

        if ($this->photos_limit === 0) {
            return true; // Unlimited
        }

        return ($this->photos_used + $count) <= $this->photos_limit;
    }

    /**
     * Use photos (increment photos_used)
     */
    public function usePhotos(int $count = 1): bool
    {
        if (!$this->canUsePhotos($count)) {
            return false;
        }

        $this->increment('photos_used', $count);
        return true;
    }

    /**
     * Get remaining photos
     */
    public function remainingPhotos(): int
    {
        if ($this->photos_limit === 0) {
            return PHP_INT_MAX; // Unlimited
        }

        return max(0, $this->photos_limit - $this->photos_used);
    }

    /**
     * Check if subscription is active
     */
    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    /**
     * Check if subscription is expired
     */
    public function isExpired(): bool
    {
        if (!$this->end_date) {
            return false;
        }

        return $this->end_date->isPast();
    }

    /**
     * Check if subscription is in trial
     */
    public function isTrial(): bool
    {
        return $this->status === SubscriptionStatus::TRIAL;
    }

    /**
     * Scope to get active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            SubscriptionStatus::ACTIVE->value,
            SubscriptionStatus::TRIAL->value
        ]);
    }

    /**
     * Scope to get subscriptions by platform
     */
    public function scopeForPlatform($query, string $platform)
    {
        return $query->where('platform', $platform);
    }

    /**
     * Scope to get expired subscriptions
     */
    public function scopeExpired($query)
    {
        return $query->where('end_date', '<=', now())
                     ->where('status', '!=', SubscriptionStatus::EXPIRED->value);
    }
}
