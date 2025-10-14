<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'display_name' => $this->display_name,
            'platform' => $this->platform,
            'price' => $this->price,
            'formatted_price' => $this->currency . ' ' . number_format($this->price, 2),
            'currency' => $this->currency,
            'billing_cycle' => $this->billing_cycle,
            'photos_limit' => $this->photos_limit,
            'photos_limit_text' => $this->photos_limit === 0 ? 'Unlimited' : number_format($this->photos_limit),
            'description' => $this->description,
            'features' => $this->features ?? [],
            'metadata' => $this->metadata ?? [],
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
