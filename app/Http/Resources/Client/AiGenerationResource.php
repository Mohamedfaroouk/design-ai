<?php

namespace App\Http\Resources\Client;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AiGenerationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        // Extract original image from input_data
        $originalImageUrl = null;
        if (isset($this->input_data['image_urls']) && is_array($this->input_data['image_urls'])) {
            $originalImageUrl = $this->input_data['image_urls'][0] ?? null;
        } elseif (isset($this->input_data['productImageUrl'])) {
            $originalImageUrl = $this->input_data['productImageUrl'];
        }

        // Extract generated image from output_data
        $generatedImageUrl = null;
        if ($this->output_data) {
            $generatedImageUrl = $this->output_data['generated_image_url']
                ?? $this->output_data['resultUrls'][0] ?? null
                ?? $this->output_data['output'][0] ?? null
                ?? $this->output_data['images'][0] ?? null;
        }

        return [
            'id' => $this->id,
            'job_id' => $this->job_id,
            'status' => $this->status,
            'input_data' => $this->input_data,
            'output_data' => $this->output_data,
            'error_message' => $this->error_message,
            'product_name' => $this->input_data['product_name'] ?? $this->input_data['productName'] ?? null,
            'original_image_url' => $originalImageUrl,
            'generated_image_url' => $generatedImageUrl,
            'can_download' => $this->isCompleted() && !empty($generatedImageUrl),
            'can_retry' => $this->isFailed() || $this->isCompleted(),
            'started_at' => $this->started_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
