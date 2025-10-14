<?php

namespace App\Services\Store\Salla;

use App\Enums\PlatformType;
use App\Enums\StoreStatus;
use App\Models\Store;
use App\Models\User;
use App\Models\WebhookLog;
use App\Services\Store\Contracts\StoreWebhookInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SallaWebhookService implements StoreWebhookInterface
{
    protected PlatformType $platform = PlatformType::SALLA;

    public function __construct(
        protected SallaSubscriptionService $sallaSubscriptionService
    ) {
    }

    public function handleStoreAuthorized(array $data): void
    {
        $this->logWebhook('app.store.authorize', $data);

        try {
            $authService = app(SallaAuthService::class);
            $authService->handleAuthorizationWebhook($data);

            $this->markWebhookAsProcessed($data);
        } catch (\Exception $e) {
            $this->markWebhookAsFailed($data, $e->getMessage());
            throw $e;
        }
    }

    public function handleStoreInstalled(array $data): void
    {
        $this->logWebhook('app.installed', $data);

        try {
            DB::transaction(function () use ($data) {
                $store = $this->findStoreByMerchantId($data['merchant']);

                if ($store) {
                    $store->update(['status' => StoreStatus::ACTIVE]);
                }
            });

            $this->markWebhookAsProcessed($data);
        } catch (\Exception $e) {
            $this->markWebhookAsFailed($data, $e->getMessage());
            throw $e;
        }
    }

    public function handleStoreUninstalled(array $data): void
    {
        $this->logWebhook('app.uninstalled', $data);

        try {
            DB::transaction(function () use ($data) {
                $store = $this->findStoreByMerchantId($data['merchant']);

                if ($store) {
                    $store->update(['status' => StoreStatus::INACTIVE]);
                }
            });

            $this->markWebhookAsProcessed($data);
        } catch (\Exception $e) {
            $this->markWebhookAsFailed($data, $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle subscription started webhook from Salla
     */
    public function handleSubscriptionStarted(array $data): void
    {
        $this->logWebhook('subscription.started', $data);

        try {
            $this->sallaSubscriptionService->handleSubscriptionStarted($data);
            $this->markWebhookAsProcessed($data);

            Log::info('Subscription started webhook processed', [
                'merchant_id' => $data['merchant']
            ]);
        } catch (\Exception $e) {
            $this->markWebhookAsFailed($data, $e->getMessage());
            Log::error('Failed to process subscription.started webhook', [
                'merchant_id' => $data['merchant'],
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Handle subscription renewed webhook from Salla
     */
    public function handleSubscriptionRenewed(array $data): void
    {
        $this->logWebhook('subscription.renewed', $data);

        try {
            $this->sallaSubscriptionService->handleSubscriptionRenewed($data);
            $this->markWebhookAsProcessed($data);

            Log::info('Subscription renewed webhook processed', [
                'merchant_id' => $data['merchant']
            ]);
        } catch (\Exception $e) {
            $this->markWebhookAsFailed($data, $e->getMessage());
            Log::error('Failed to process subscription.renewed webhook', [
                'merchant_id' => $data['merchant'],
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Handle subscription expired webhook from Salla
     */
    public function handleSubscriptionExpired(array $data): void
    {
        $this->logWebhook('subscription.expired', $data);

        try {
            $this->sallaSubscriptionService->handleSubscriptionExpired($data);
            $this->markWebhookAsProcessed($data);

            Log::info('Subscription expired webhook processed', [
                'merchant_id' => $data['merchant']
            ]);
        } catch (\Exception $e) {
            $this->markWebhookAsFailed($data, $e->getMessage());
            Log::error('Failed to process subscription.expired webhook', [
                'merchant_id' => $data['merchant'],
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Handle trial started webhook from Salla
     */
    public function handleTrialStarted(array $data): void
    {
        $this->logWebhook('trial.started', $data);

        try {
            $this->sallaSubscriptionService->handleTrialStarted($data);
            $this->markWebhookAsProcessed($data);

            Log::info('Trial started webhook processed', [
                'merchant_id' => $data['merchant']
            ]);
        } catch (\Exception $e) {
            $this->markWebhookAsFailed($data, $e->getMessage());
            Log::error('Failed to process trial.started webhook', [
                'merchant_id' => $data['merchant'],
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Handle trial expired webhook from Salla
     */
    public function handleTrialExpired(array $data): void
    {
        $this->logWebhook('trial.expired', $data);

        try {
            $this->sallaSubscriptionService->handleTrialExpired($data);
            $this->markWebhookAsProcessed($data);

            Log::info('Trial expired webhook processed', [
                'merchant_id' => $data['merchant']
            ]);
        } catch (\Exception $e) {
            $this->markWebhookAsFailed($data, $e->getMessage());
            Log::error('Failed to process trial.expired webhook', [
                'merchant_id' => $data['merchant'],
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function validateWebhook(array $headers, string $payload): bool
    {
        // Salla webhook validation
        // Check if authorization header exists
        if (!isset($headers['authorization'])) {
            return false;
        }

        // In production, implement proper signature validation
        // For now, just check if authorization header is present
        return true;
    }

    protected function logWebhook(string $event, array $data): WebhookLog
    {
        $store = $this->findStoreByMerchantId($data['merchant']);

        return WebhookLog::create([
            'event' => $event,
            'platform' => $this->platform,
            'merchant_id' => $data['merchant'],
            'user_id' => $store?->user_id,
            'store_id' => $store?->id,
            'payload' => $data,
            'status' => 'pending',
            'webhook_created_at' => $data['created_at'] ?? now(),
        ]);
    }

    protected function markWebhookAsProcessed(array $data): void
    {
        WebhookLog::where('merchant_id', $data['merchant'])
                  ->where('platform', $this->platform)
                  ->where('status', 'pending')
                  ->latest()
                  ->first()
                  ?->markAsProcessed();
    }

    protected function markWebhookAsFailed(array $data, string $error): void
    {
        WebhookLog::where('merchant_id', $data['merchant'])
                  ->where('platform', $this->platform)
                  ->where('status', 'pending')
                  ->latest()
                  ->first()
                  ?->markAsFailed($error);
    }

    protected function findStoreByMerchantId(string $merchantId): ?Store
    {
        return Store::where('merchant_id', $merchantId)
                    ->where('platform', $this->platform)
                    ->first();
    }
}
