<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    /**
     * Transform the resource into an array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'platform' => [
                'value' => $this->platform->value,
                'label' => $this->platform->label(),
            ],
            'merchant_id' => $this->merchant_id,
            'store_id' => $this->store_id,
            'domain' => $this->domain,
            'store_name' => $this->store_name,
            'store_email' => $this->store_email,
            'store_phone' => $this->store_phone,
            'avatar' => $this->avatar,
            'status' => [
                'value' => $this->status->value,
                'label' => $this->status->label(),
            ],
            'token_expires_at' => $this->token_expires_at?->toISOString(),
            'is_token_expired' => $this->isTokenExpired(),
            'needs_token_refresh' => $this->needsTokenRefresh(),
            'metadata' => $this->metadata,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'webhook_logs_count' => $this->whenLoaded('webhookLogs', fn() => $this->webhookLogs->count()),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
