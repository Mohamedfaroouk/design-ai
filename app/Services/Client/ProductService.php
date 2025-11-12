<?php

namespace App\Services\Client;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProductService
{
    public function create(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            // Add authenticated user ID
            $data['user_id'] = Auth::id();

            // Check for duplicate product from same platform
            if (isset($data['platform_product_id']) && $data['platform'] !== 'others') {
                $existing = Product::where('user_id', $data['user_id'])
                    ->where('platform', $data['platform'])
                    ->where('platform_product_id', $data['platform_product_id'])
                    ->first();

                if ($existing) {
                    return $existing;
                }
            }

            return Product::create($data);
        });
    }

    public function update(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            $product->update($data);
            return $product->fresh();
        });
    }

    public function delete(Product $product): bool
    {
        return DB::transaction(fn () => $product->delete());
    }

    public function findOrCreateFromPlatform(array $platformData): Product
    {
        return DB::transaction(function () use ($platformData) {
            $platformData['user_id'] = Auth::id();

            // For Salla/Zid products, prevent duplicates
            if ($platformData['platform'] !== 'others' && isset($platformData['platform_product_id'])) {
                $existing = Product::where('user_id', $platformData['user_id'])
                    ->where('platform', $platformData['platform'])
                    ->where('platform_product_id', $platformData['platform_product_id'])
                    ->first();

                if ($existing) {
                    return $existing;
                }
            }

            return Product::create($platformData);
        });
    }
}
