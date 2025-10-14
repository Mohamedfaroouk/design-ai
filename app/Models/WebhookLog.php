<?php

namespace App\Models;

use App\Enums\PlatformType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookLog extends Model
{
    protected $fillable = [
        'event',
        'platform',
        'merchant_id',
        'user_id',
        'store_id',
        'payload',
        'status',
        'error_message',
        'processed_at',
        'webhook_created_at',
    ];

    protected $casts = [
        'platform' => PlatformType::class,
        'payload' => 'array',
        'processed_at' => 'datetime',
        'webhook_created_at' => 'datetime',
    ];

    /**
     * Get the user associated with the webhook
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the store associated with the webhook
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Mark webhook as processed
     */
    public function markAsProcessed(): void
    {
        $this->update([
            'status' => 'processed',
            'processed_at' => now(),
        ]);
    }

    /**
     * Mark webhook as failed
     */
    public function markAsFailed(string $error): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $error,
            'processed_at' => now(),
        ]);
    }

    /**
     * Scope to get pending webhooks
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get webhooks by platform
     */
    public function scopePlatform($query, PlatformType $platform)
    {
        return $query->where('platform', $platform);
    }

    /**
     * Scope to get webhooks by event
     */
    public function scopeEvent($query, string $event)
    {
        return $query->where('event', $event);
    }
}
