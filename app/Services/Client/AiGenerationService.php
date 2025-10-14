<?php

namespace App\Services\Client;

use App\Models\AIGenerationJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AiGenerationService
{
    /**
     * Retry a failed or completed AI generation job
     */
    public function retry(AIGenerationJob $job): AIGenerationJob
    {
        return DB::transaction(function () use ($job) {
            // Create a new job with the same input data
            $newJob = AIGenerationJob::create([
                'user_id' => $job->user_id,
                'job_id' => 'job_' . uniqid() . '_' . time(),
                'status' => AIGenerationJob::STATUS_PENDING,
                'input_data' => $job->input_data,
            ]);

            // Here you would dispatch the job to your queue/AI service
            // For now, we just create the job record
            // dispatch(new ProcessAIGeneration($newJob));

            return $newJob;
        });
    }

    /**
     * Download the generated image
     * Handles both local storage paths and external URLs
     */
    public function download(AIGenerationJob $job): StreamedResponse|\Illuminate\Http\RedirectResponse
    {
        // Get the generated image data from output_data
        $outputData = $job->output_data;

        // Try to get image URL or path
        $imageUrl = $outputData['generated_image_url'] ?? $outputData['resultUrls'][0] ?? null;
        $imagePath = $outputData['generated_image_path'] ?? null;

        // If we have a local path, serve from storage
        if ($imagePath && Storage::exists($imagePath)) {
            $filename = $outputData['filename'] ?? 'generated-image-' . $job->id . '.png';
            return Storage::download($imagePath, $filename);
        }

        // If we have an external URL, redirect to it
        if ($imageUrl) {
            return redirect($imageUrl);
        }

        // No image available
        abort(404, __('ai_generation.errors.no_image'));
    }
}
