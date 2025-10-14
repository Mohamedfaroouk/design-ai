<?php

namespace App\Models;

use App\Enums\BillingCycle;
use App\Enums\PlatformType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'platform',
        'price',
        'currency',
        'billing_cycle',
        'photos_limit',
        'is_active',
        'is_featured',
        'sort_order',
        'features',
        'metadata',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'photos_limit' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
        'features' => 'array',
        'metadata' => 'array',
        'billing_cycle' => BillingCycle::class,
    ];

    /**
     * Get subscriptions using this package
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Scope to get active packages
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get packages by platform
     */
    public function scopeForPlatform($query, string $platform)
    {
        return $query->where(function ($q) use ($platform) {
            $q->where('platform', $platform)
              ->orWhere('platform', 'all');
        });
    }

    /**
     * Scope to get featured packages
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Get package by name (used by Salla)
     */
    public static function findByName(string $name): ?Package
    {
        return static::where('name', $name)->first();
    }

    /**
     * Check if package is available for platform
     */
    public function isAvailableFor(string $platform): bool
    {
        return $this->platform === 'all' || $this->platform === $platform;
    }
}
