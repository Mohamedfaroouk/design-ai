<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\Client\AiGenerationResource;
use App\Models\AIGenerationJob;
use App\Services\Client\AiGenerationService;
use App\Traits\HasDataTable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiGenerationController extends Controller
{
    use HasDataTable;

    public function __construct(
        private readonly AiGenerationService $service
    ) {}

    /**
     * Display a listing of AI generation jobs for the authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        $query = AIGenerationJob::query()
            ->where('user_id', $request->user()->id)
            ->with(['user'])
            ->latest();

        return $this->dataTableResponse(
            query: $query,
            request: $request,
            resource: AiGenerationResource::class,
            searchable: ['job_id', 'status', 'error_message'],
            filterable: ['status']
        );
    }

    /**
     * Retry a failed AI generation job
     */
    public function retry(AIGenerationJob $aiGenerationJob): JsonResponse
    {
        // Clients can only retry their own jobs
        if ($aiGenerationJob->user_id !== auth()->id()) {
            abort(403, __('ai_generation.errors.unauthorized'));
        }

        // Retry the job
        $newJob = $this->service->retry($aiGenerationJob);

        return response()->json([
            'message' => __('ai_generation.messages.retry_success'),
            'data' => new AiGenerationResource($newJob)
        ]);
    }

    /**
     * Download generated image
     */
    public function download(AIGenerationJob $aiGenerationJob): mixed
    {
        // Clients can only download their own jobs
        if ($aiGenerationJob->user_id !== auth()->id()) {
            abort(403, __('ai_generation.errors.unauthorized'));
        }

        // Ensure job is completed
        if (!$aiGenerationJob->isCompleted()) {
            abort(400, __('ai_generation.errors.not_completed'));
        }

        return $this->service->download($aiGenerationJob);
    }
}
