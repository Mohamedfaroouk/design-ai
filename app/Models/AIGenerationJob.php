<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AIGenerationJob extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $table = 'ai_generation_jobs';
    protected $fillable = [
        'user_id',
        'job_id',
        'status',
        'input_data',
        'output_data',
        'error_message',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'input_data' => 'array',
        'output_data' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Statuses
     */
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    /**
     * Get the user that owns the job
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark job as processing
     */
    public function markAsProcessing(): void
    {
        $this->update([
            'status' => self::STATUS_PROCESSING,
            'started_at' => now(),
        ]);
    }

    /**
     * Mark job as completed
     */
    public function markAsCompleted(array $outputData): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'output_data' => $outputData,
            'completed_at' => now(),
        ]);
    }

    /**
     * Mark job as failed
     */
    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'error_message' => $errorMessage,
            'completed_at' => now(),
        ]);
    }

    /**
     * Check if job is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if job is processing
     */
    public function isProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    /**
     * Check if job is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if job is failed
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Register media conversions for thumbnails
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        // Small thumbnail for table listings (100x100)
        $this->addMediaConversion('thumb')
            ->width(100)
            ->height(100)
            ->sharpen(10)
            ->nonQueued()
            ->performOnCollections('ai-images');

        // Medium thumbnail for preview (300x300)
        $this->addMediaConversion('preview')
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->nonQueued()
            ->performOnCollections('ai-images');

        // Large optimized version for full view (1024x1024)
        $this->addMediaConversion('large')
            ->width(1024)
            ->height(1024)
            ->sharpen(5)
            ->performOnCollections('ai-images');
    }

    /**
     * Register media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('ai-images')
            ->useFallbackUrl('/images/placeholder.png')
            ->useFallbackPath(public_path('/images/placeholder.png'));
    }
}
