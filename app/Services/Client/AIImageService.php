<?php

namespace App\Services\Client;

use App\Models\AIGenerationJob;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AIImageService
{
    protected string $apiKey;
    protected string $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('services.kie_ai.api_key');
        $this->apiUrl = config('services.kie_ai.api_url', 'https://api.kie.ai/api/v1');
    }

    /**
     * Generate multiple AI images (batch processing)
     */
    public function generateBatch(array $products, string $imageSize = '1:1', string $outputFormat = 'png'): \Illuminate\Support\Collection
    {
        return DB::transaction(function () use ($products, $imageSize, $outputFormat) {
            $jobs = collect();

            foreach ($products as $product) {
                $data = array_merge($product, [
                    'image_size' => $imageSize,
                    'output_format' => $outputFormat,
                ]);

                try {
                    $job = $this->generateImage($data);
                    $jobs->push($job);
                } catch (\Exception $e) {
                    // Log error but continue with other products
                    Log::error('Batch generation error for product', [
                        'product' => $product['product_name'] ?? 'Unknown',
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            return $jobs;
        });
    }

    /**
     * Generate a single AI image using Kie.ai API and save to database
     */
    public function generateImage(array $data): AIGenerationJob
    {
        return DB::transaction(function () use ($data) {
            $userId = auth()->id();
            $prompt = $data['prompt'];
            $imageUrls = $data['image_urls'] ?? [];
            $imageSize = $data['image_size'] ?? '1:1';
            $outputFormat = $data['output_format'] ?? 'png';

            // Generate callback URL
            $callbackUrl = route('client.ai.callback');

            try {
                // Build the input object for google/nano-banana-edit model
                $input = [
                    'prompt' => $prompt,
                    'output_format' => $outputFormat,
                    'image_size' => $imageSize,
                ];

                // Add image_urls to input if provided
                if (!empty($imageUrls)) {
                    $input['image_urls'] = $imageUrls;
                }

                // Build the main payload
                $payload = [
                    'model' => 'google/nano-banana-edit',
                    'input' => $input,
                    'callBackUrl' => $callbackUrl,
                ];

                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->apiKey,
                ])->post($this->apiUrl . '/jobs/createTask', $payload);

                if ($response->failed()) {
                    Log::error('Kie.ai API Error', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);

                    throw new \Exception('Failed to generate image: ' . $response->body());
                }

                $apiResponse = $response->json();

                // Extract job ID from nested response
                $jobId = $apiResponse['data']['taskId'] ?? $apiResponse['data']['recordId'] ?? null;

                if (!$jobId) {
                    throw new \Exception('No job ID returned from API');
                }

                // Create database record
                $job = AIGenerationJob::create([
                    'user_id' => $userId,
                    'job_id' => $jobId,
                    'status' => AIGenerationJob::STATUS_PENDING,
                    'input_data' => $data,
                ]);

                return $job;
            } catch (\Exception $e) {
                Log::error('AI Image Generation Error', [
                    'error' => $e->getMessage(),
                    'data' => $data,
                ]);

                throw $e;
            }
        });
    }

    /**
     * Check the status of a generation job from Kie.ai API and update database
     */
    public function checkJobStatus(string $jobId): AIGenerationJob
    {
        $job = AIGenerationJob::where('job_id', $jobId)->firstOrFail();

        // If job is already completed or failed, return cached result
        if ($job->isCompleted() || $job->isFailed()) {
            return $job;
        }

        try {
            // Call Kie.ai recordInfo API to get latest status
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->apiUrl . '/jobs/recordInfo', [
                'taskId' => $jobId,
            ]);

            if ($response->successful()) {
                $apiData = $response->json();

                // Extract status from response
                // Response structure: { code, msg, data: { state, taskId, resultJson, etc } }
                $responseData = $apiData['data'] ?? $apiData;
                $status = strtolower($responseData['state'] ?? '');

                // Parse resultJson if available
                if (isset($responseData['resultJson']) && is_string($responseData['resultJson'])) {
                    $resultData = json_decode($responseData['resultJson'], true);
                    if ($resultData && isset($resultData['resultUrls'])) {
                        $responseData['output'] = $resultData['resultUrls'];
                        $responseData['images'] = $resultData['resultUrls'];
                    }
                }

                // Update job based on status
                if (in_array($status, ['completed', 'success', 'succeeded'])) {
                    $job->markAsCompleted($responseData);
                } elseif (in_array($status, ['failed', 'error', 'fail'])) {
                    $errorMessage = $responseData['error'] ?? $responseData['message'] ?? 'Generation failed';
                    $job->markAsFailed($errorMessage);
                } elseif (in_array($status, ['processing', 'running', 'waiting'])) {
                    $job->markAsProcessing();
                }

                Log::info('Job status checked via API', [
                    'job_id' => $jobId,
                    'status' => $status,
                    'has_output' => isset($responseData['output']),
                ]);
            } else {
                Log::warning('Failed to check job status from API', [
                    'job_id' => $jobId,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Job status check error', [
                'job_id' => $jobId,
                'error' => $e->getMessage(),
            ]);
        }

        // Return job with latest status from database
        return $job->fresh();
    }

    /**
     * Handle callback from Kie.ai
     * Processes both success (code: 200) and failure (code: 501) callbacks
     */
    public function handleCallback(array $data): void
    {
        try {
            Log::info('Processing Kie.ai callback', ['data' => $data]);

            // Extract response code and data
            $code = $data['code'] ?? null;
            $responseData = $data['data'] ?? [];
            $message = $data['msg'] ?? '';

            // Extract job ID (taskId)
            $jobId = $responseData['taskId'] ?? null;

            if (!$jobId) {
                Log::error('Callback missing taskId', ['data' => $data]);
                return;
            }

            // Find the job in database
            $job = AIGenerationJob::where('job_id', $jobId)->first();

            if (!$job) {
                Log::error('Job not found for callback', ['job_id' => $jobId, 'data' => $data]);
                return;
            }

            // Get state from callback
            $state = strtolower($responseData['state'] ?? '');

            // Handle based on callback code and state
            if ($code === 200 && $state === 'success') {
                // Success callback - extract generated image URLs
                $this->handleSuccessCallback($job, $responseData);
            } elseif ($code === 501 || $state === 'fail') {
                // Failure callback - extract error message
                $this->handleFailureCallback($job, $responseData);
            } elseif (in_array($state, ['processing', 'running', 'waiting', 'pending'])) {
                // Job is still processing
                $job->markAsProcessing();
                Log::info('Job processing via callback', [
                    'job_id' => $jobId,
                    'state' => $state,
                ]);
            } else {
                Log::warning('Unknown callback state', [
                    'job_id' => $jobId,
                    'code' => $code,
                    'state' => $state,
                    'message' => $message,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Callback handling error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data,
            ]);
        }
    }

    /**
     * Handle successful generation callback
     */
    private function handleSuccessCallback(AIGenerationJob $job, array $data): void
    {
        // Parse resultJson to extract image URLs
        $resultJson = $data['resultJson'] ?? null;
        $imageUrls = [];

        if ($resultJson) {
            $resultData = is_string($resultJson) ? json_decode($resultJson, true) : $resultJson;

            if ($resultData && isset($resultData['resultUrls'])) {
                $imageUrls = $resultData['resultUrls'];
            }
        }

        // Prepare output data
        $outputData = [
            'taskId' => $data['taskId'] ?? null,
            'state' => $data['state'] ?? 'success',
            'resultUrls' => $imageUrls,
            'output' => $imageUrls, // For backward compatibility
            'images' => $imageUrls, // For backward compatibility
            'generated_image_url' => $imageUrls[0] ?? null, // Primary image
            'generated_image_path' => null, // Will be set if we download/store locally
            'filename' => 'generated-' . $job->id . '.png',
            'consumeCredits' => $data['consumeCredits'] ?? null,
            'costTime' => $data['costTime'] ?? null,
            'completeTime' => $data['completeTime'] ?? null,
        ];

        // Mark job as completed with output data
        $job->markAsCompleted($outputData);

        Log::info('Job completed successfully via callback', [
            'job_id' => $job->job_id,
            'image_count' => count($imageUrls),
            'images' => $imageUrls,
        ]);
    }

    /**
     * Handle failed generation callback
     */
    private function handleFailureCallback(AIGenerationJob $job, array $data): void
    {
        // Extract error information
        $failCode = $data['failCode'] ?? '500';
        $failMsg = $data['failMsg'] ?? 'Unknown error';
        $errorMessage = "[$failCode] $failMsg";

        // Mark job as failed
        $job->markAsFailed($errorMessage);

        Log::error('Job failed via callback', [
            'job_id' => $job->job_id,
            'fail_code' => $failCode,
            'fail_message' => $failMsg,
        ]);
    }
}
