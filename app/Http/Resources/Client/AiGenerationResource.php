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
        $downloadedImages = [];
        if ($this->output_data) {
            // Use locally stored image if available (from downloaded_images)
            if (isset($this->output_data['downloaded_images']) && is_array($this->output_data['downloaded_images'])) {
                $downloadedImages = $this->output_data['downloaded_images'];
                $generatedImageUrl = $downloadedImages[0]['stored_url'] ?? null;
            }

            // Fallback to original URLs if no downloaded images
            if (!$generatedImageUrl) {
                $generatedImageUrl = $this->output_data['generated_image_url']
                    ?? $this->output_data['resultUrls'][0] ?? null
                    ?? $this->output_data['output'][0] ?? null
                    ?? $this->output_data['images'][0] ?? null;
            }
        }

        // Get media thumbnails from Spatie Media Library
        $thumbnails = [];
        $media = $this->getFirstMedia('ai-images');
        if ($media) {
            $thumbnails = [
                'thumb' => $media->getUrl('thumb'),
                'preview' => $media->getUrl('preview'),
                'large' => $media->getUrl('large'),
                'original' => $media->getUrl(),
            ];
        } elseif (!empty($downloadedImages)) {
            // Fallback to downloaded_images data
            $thumbnails = [
                'thumb' => $downloadedImages[0]['thumb_url'] ?? null,
                'preview' => $downloadedImages[0]['preview_url'] ?? null,
                'large' => $downloadedImages[0]['large_url'] ?? null,
                'original' => $downloadedImages[0]['stored_url'] ?? null,
            ];
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
            'thumbnails' => $thumbnails,
            'downloaded_images' => $downloadedImages,
            'can_download' => $this->isCompleted() && !empty($generatedImageUrl),
            'can_retry' => $this->isFailed() || $this->isCompleted(),
            'started_at' => $this->started_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
