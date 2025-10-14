<?php

namespace App\Services\Store\Salla;

use App\Enums\PlatformType;
use App\Enums\SubscriptionStatus;
use App\Models\Package;
use App\Models\Store;
use App\Models\Subscription;
use App\Models\SubscriptionHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SallaSubscriptionService
{
    /**
     * Handle subscription started event from Salla
     */
    public function handleSubscriptionStarted(array $data): ?Subscription
    {
        return DB::transaction(function () use ($data) {
            $user = $this->findUserByMerchant($data['merchant']);

            if (!$user) {
                Log::error('User not found for subscription start', ['merchant_id' => $data['merchant']]);
                return null;
            }

            $package = $this->findPackageByPlanName($data['data']['plan_name'] ?? null, PlatformType::SALLA);

            if (!$package) {
                Log::warning('Package not found for subscription', [
                    'plan_name' => $data['data']['plan_name'] ?? 'unknown',
                    'merchant_id' => $data['merchant']
                ]);
            }

            // Check for existing subscription
            $subscription = Subscription::where('user_id', $user->id)
                                      ->where('platform', PlatformType::SALLA)
                                      ->first();

            $isNew = !$subscription;
            $eventType = $isNew ? 'started' : 'upgraded';

            if ($isNew) {
                $subscription = new Subscription();
                $subscription->user_id = $user->id;
                $subscription->platform = PlatformType::SALLA;
            }

            $oldPackageData = $subscription->package_data ?? null;

            // Update subscription
            $subscription->package_id = $package?->id;
            $subscription->merchant_id = $data['merchant'];
            $subscription->subscription_id = $data['data']['subscription_id'] ?? null;
            $subscription->status = SubscriptionStatus::ACTIVE;
            $subscription->package_data = $package ? $package->toArray() : $data['data'];
            $subscription->photos_limit = $package?->photos_limit ?? 0;
            $subscription->start_date = $data['data']['start_date'] ?? now();
            $subscription->end_date = $data['data']['end_date'] ?? null;
            $subscription->metadata = $data['data'] ?? [];

            // Reset photos_used if it's a new subscription
            if ($isNew) {
                $subscription->photos_used = 0;
            }

            $subscription->save();

            // Create history record
            $this->createHistory($subscription, $eventType, $data, $oldPackageData);

            Log::info('Subscription started/updated', [
                'subscription_id' => $subscription->id,
                'user_id' => $user->id,
                'package_id' => $package?->id,
                'event_type' => $eventType
            ]);

            return $subscription;
        });
    }

    /**
     * Handle subscription renewed event from Salla
     */
    public function handleSubscriptionRenewed(array $data): ?Subscription
    {
        return DB::transaction(function () use ($data) {
            $user = $this->findUserByMerchant($data['merchant']);

            if (!$user) {
                Log::error('User not found for subscription renewal', ['merchant_id' => $data['merchant']]);
                return null;
            }

            $subscription = Subscription::where('user_id', $user->id)
                                      ->where('platform', PlatformType::SALLA)
                                      ->first();

            if (!$subscription) {
                Log::error('Subscription not found for renewal', ['user_id' => $user->id]);
                return null;
            }

            $package = $this->findPackageByPlanName($data['data']['plan_name'] ?? null, PlatformType::SALLA);

            $oldPackageData = $subscription->package_data;

            // Update subscription
            if ($package) {
                $subscription->package_id = $package->id;
                $subscription->package_data = $package->toArray();
                $subscription->photos_limit = $package->photos_limit;
            }

            $subscription->status = SubscriptionStatus::ACTIVE;
            $subscription->start_date = $data['data']['start_date'] ?? $subscription->start_date;
            $subscription->end_date = $data['data']['end_date'] ?? null;
            $subscription->metadata = $data['data'] ?? [];

            $subscription->save();

            // Create history record
            $this->createHistory($subscription, 'renewed', $data, $oldPackageData);

            Log::info('Subscription renewed', [
                'subscription_id' => $subscription->id,
                'user_id' => $user->id
            ]);

            return $subscription;
        });
    }

    /**
     * Handle subscription expired event from Salla
     */
    public function handleSubscriptionExpired(array $data): ?Subscription
    {
        return DB::transaction(function () use ($data) {
            $user = $this->findUserByMerchant($data['merchant']);

            if (!$user) {
                return null;
            }

            $subscription = Subscription::where('user_id', $user->id)
                                      ->where('platform', PlatformType::SALLA)
                                      ->first();

            if (!$subscription) {
                return null;
            }

            $oldPackageData = $subscription->package_data;

            $subscription->status = SubscriptionStatus::EXPIRED;
            $subscription->save();

            // Create history record
            $this->createHistory($subscription, 'expired', $data, $oldPackageData);

            Log::info('Subscription expired', [
                'subscription_id' => $subscription->id,
                'user_id' => $user->id
            ]);

            return $subscription;
        });
    }

    /**
     * Handle trial started event from Salla
     */
    public function handleTrialStarted(array $data): ?Subscription
    {
        return DB::transaction(function () use ($data) {
            $user = $this->findUserByMerchant($data['merchant']);

            if (!$user) {
                return null;
            }

            $package = Package::where('platform', PlatformType::SALLA)
                            ->where('name', 'free_trial')
                            ->first();

            $subscription = Subscription::where('user_id', $user->id)
                                      ->where('platform', PlatformType::SALLA)
                                      ->first();

            $isNew = !$subscription;

            if ($isNew) {
                $subscription = new Subscription();
                $subscription->user_id = $user->id;
                $subscription->platform = PlatformType::SALLA;
                $subscription->photos_used = 0;
            }

            $oldPackageData = $subscription->package_data ?? null;

            $subscription->package_id = $package?->id;
            $subscription->merchant_id = $data['merchant'];
            $subscription->status = SubscriptionStatus::TRIAL;
            $subscription->package_data = $package ? $package->toArray() : $data['data'];
            $subscription->photos_limit = $package?->photos_limit ?? 0;
            $subscription->start_date = $data['data']['start_date'] ?? now();
            $subscription->end_date = $data['data']['end_date'] ?? null;
            $subscription->trial_ends_at = $data['data']['end_date'] ?? null;
            $subscription->metadata = $data['data'] ?? [];

            $subscription->save();

            // Create history record
            $this->createHistory($subscription, 'trial_started', $data, $oldPackageData);

            Log::info('Trial started', [
                'subscription_id' => $subscription->id,
                'user_id' => $user->id
            ]);

            return $subscription;
        });
    }

    /**
     * Handle trial expired event from Salla
     */
    public function handleTrialExpired(array $data): ?Subscription
    {
        return DB::transaction(function () use ($data) {
            $user = $this->findUserByMerchant($data['merchant']);

            if (!$user) {
                return null;
            }

            $subscription = Subscription::where('user_id', $user->id)
                                      ->where('platform', PlatformType::SALLA)
                                      ->first();

            if (!$subscription) {
                return null;
            }

            $oldPackageData = $subscription->package_data;

            $subscription->status = SubscriptionStatus::EXPIRED;
            $subscription->save();

            // Create history record
            $this->createHistory($subscription, 'trial_expired', $data, $oldPackageData);

            Log::info('Trial expired', [
                'subscription_id' => $subscription->id,
                'user_id' => $user->id
            ]);

            return $subscription;
        });
    }

    /**
     * Find user by merchant ID
     */
    protected function findUserByMerchant(string $merchantId): ?User
    {
        $store = Store::where('merchant_id', $merchantId)
                     ->where('platform', PlatformType::SALLA)
                     ->first();

        return $store?->user;
    }

    /**
     * Find package by plan name
     */
    protected function findPackageByPlanName(?string $planName, PlatformType $platform): ?Package
    {
        if (!$planName) {
            return null;
        }

        return Package::where('name', $planName)
                     ->where(function ($query) use ($platform) {
                         $query->where('platform', $platform->value)
                               ->orWhere('platform', 'all');
                     })
                     ->first();
    }

    /**
     * Create subscription history record
     */
    protected function createHistory(
        Subscription $subscription,
        string $eventType,
        array $webhookData,
        ?array $oldPackageData
    ): SubscriptionHistory {
        $changes = null;

        if ($oldPackageData && $eventType === 'upgraded') {
            $changes = [
                'old_package' => $oldPackageData,
                'new_package' => $subscription->package_data,
            ];
        }

        return SubscriptionHistory::create([
            'subscription_id' => $subscription->id,
            'user_id' => $subscription->user_id,
            'package_id' => $subscription->package_id,
            'platform' => $subscription->platform,
            'event_type' => $eventType,
            'status' => $subscription->status->value,
            'package_data' => $subscription->package_data,
            'changes' => $changes,
            'price' => $webhookData['data']['price'] ?? null,
            'start_date' => $subscription->start_date,
            'end_date' => $subscription->end_date,
            'webhook_payload' => $webhookData,
        ]);
    }
}
