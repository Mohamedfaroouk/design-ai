<?php

namespace App\Http\Controllers\Integration;

use App\Http\Controllers\Controller;
use App\Services\Store\Salla\SallaWebhookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SallaWebhookController extends Controller
{
    public function __construct(
        protected SallaWebhookService $sallaWebhookService
    ) {
    }

    /**
     * Handle incoming Salla webhooks
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            // Validate webhook (optional but recommended)
            $headers = $request->headers->all();
            $payload = $request->getContent();

            if (!$this->sallaWebhookService->validateWebhook($headers, $payload)) {
                return response()->json([
                    'message' => 'Invalid webhook signature'
                ], 401);
            }

            $data = $request->all();
            $event = $data['event'] ?? null;

            if (!$event) {
                return response()->json([
                    'message' => 'Missing event type'
                ], 400);
            }

            // Route webhook to appropriate handler
            match ($event) {
                'app.store.authorize' => $this->sallaWebhookService->handleStoreAuthorized($data),
                'app.installed' => $this->sallaWebhookService->handleStoreInstalled($data),
                'app.uninstalled' => $this->sallaWebhookService->handleStoreUninstalled($data),
                'subscription.started' => $this->sallaWebhookService->handleSubscriptionStarted($data),
                'subscription.renewed' => $this->sallaWebhookService->handleSubscriptionRenewed($data),
                'subscription.expired' => $this->sallaWebhookService->handleSubscriptionExpired($data),
                'trial.started' => $this->sallaWebhookService->handleTrialStarted($data),
                'trial.expired' => $this->sallaWebhookService->handleTrialExpired($data),
                default => Log::info('Unhandled Salla webhook event: ' . $event, ['data' => $data]),
            };

            return response()->json([
                'message' => 'Webhook processed successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Salla webhook processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $request->all()
            ]);

            return response()->json([
                'message' => 'Webhook processing failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
