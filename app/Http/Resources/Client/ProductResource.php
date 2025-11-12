<?php

namespace App\Http\Resources\Client;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'platform' => $this->platform,
            'platform_product_id' => $this->platform_product_id,
            'name' => $this->name,
            'description' => $this->description,
            'sku' => $this->sku,
            'price' => $this->price,
            'image_url' => $this->image_url,
            'metadata' => $this->metadata,
            'images_count' => $this->whenCounted('images'),
            'latest_image' => $this->whenLoaded('images', function () {
                return $this->images->first()?->image_url;
            }),
            'images' => $this->whenLoaded('images', function () {
                return $this->images->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'image_url' => $image->image_url,
                        'prompt' => $image->prompt,
                        'style' => $image->style,
                        'status' => $image->status,
                        'created_at' => $image->created_at,
                    ];
                });
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
