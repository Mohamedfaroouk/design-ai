<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'user_id',
        'platform',
        'platform_product_id',
        'name',
        'description',
        'sku',
        'price',
        'image_url',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'price' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function latestImages(): HasMany
    {
        return $this->images()->latest();
    }
}
