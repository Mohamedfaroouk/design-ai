<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\Client\AiGenerationResource;
use App\Http\Resources\Client\ProductResource;
use App\Models\AIGenerationJob;
use App\Models\Product;
use App\Services\Client\AIImageService;
use App\Traits\HasDataTableInertia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WebAIImageController extends Controller
{
    use HasDataTableInertia;

    public function __construct(
        protected AIImageService $service
    ) {}

    /**
     * Display AI generation index page with history
     */
    public function index(Request $request): Response
    {
        return $this->inertiaDataTable(
            page: 'Modules/client/AiGenerationIndex',
            query: AIGenerationJob::where('user_id', auth()->id()),
            request: $request,
            resource: AiGenerationResource::class,
            searchable: ['job_id'],
            filterable: ['status'],
            defaultSort: 'created_at',
            defaultOrder: 'desc'
        );
    }

    /**
     * Show the image wizard page
     */
    public function wizard(): Response
    {
        // Load user's products for selection in the wizard
        $products = Product::where('user_id', auth()->id())
            ->where('platform', 'others')
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        return Inertia::render('Modules/client/ImageWizard', [
            'products' => ProductResource::collection($products),
        ]);
    }

    /**
     * Generate AI images (supports batch processing for multiple products)
     * This endpoint stays as JSON for async processing
     */
    public function generate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'products' => ['required', 'array', 'min:1'],
            'products.*.prompt' => ['required', 'string', 'min:10'],
            'products.*.product_name' => ['required', 'string'],
            'products.*.product_id' => ['nullable', 'integer', 'exists:products,id'],
            'products.*.image_urls' => ['nullable', 'array'],
            'products.*.image_urls.*' => ['string', 'url'],
            'image_size' => ['nullable', 'string', 'in:1:1,16:9,9:16,4:3,3:4'],
            'output_format' => ['nullable', 'string', 'in:png,jpg,jpeg,webp'],
        ]);

        try {
            $jobs = $this->service->generateBatch(
                products: $validated['products'],
                imageSize: $validated['image_size'] ?? '1:1',
                outputFormat: $validated['output_format'] ?? 'png'
            );

            return response()->json([
                'message' => __('ai.generation.batch_started', ['count' => count($jobs)]),
                'data' => [
                    'jobs_count' => count($jobs),
                    'jobs' => $jobs->map(fn($job) => [
                        'id' => $job->id,
                        'job_id' => $job->job_id,
                        'status' => $job->status,
                        'product_name' => $job->input_data['product_name'] ?? null,
                    ]),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => __('ai.generation.failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check job status (JSON endpoint for polling)
     */
    public function status(Request $request, string $jobId): JsonResponse
    {
        try {
            $job = $this->service->checkJobStatus($jobId);

            $response = [
                'id' => $job->id,
                'job_id' => $job->job_id,
                'status' => $job->status,
                'created_at' => $job->created_at,
                'started_at' => $job->started_at,
                'completed_at' => $job->completed_at,
            ];

            // Add output data if job is completed
            if ($job->isCompleted() && $job->output_data) {
                $response['output'] = $job->output_data['output'] ?? [];
                $response['images'] = $job->output_data['images'] ?? [];
                $response['image_url'] = $job->output_data['output'][0] ?? null;
            }

            // Add error message if job failed
            if ($job->isFailed()) {
                $response['error_message'] = $job->error_message;
            }

            return response()->json([
                'data' => $response,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => __('ai.status.failed'),
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Callback endpoint for Kie.ai (stays as JSON)
     */
    public function callback(Request $request): JsonResponse
    {
        // Log the callback for debugging
        \Log::info('Kie.ai Callback Received', $request->all());

        try {
            $this->service->handleCallback($request->all());

            return response()->json([
                'message' => 'Callback processed successfully',
            ]);
        } catch (\Exception $e) {
            \Log::error('Callback processing failed', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Callback processing failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
