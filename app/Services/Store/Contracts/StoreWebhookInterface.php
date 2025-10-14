<?php

namespace App\Services\Store\Contracts;

interface StoreWebhookInterface
{
    /**
     * Handle store authorization webhook
     */
    public function handleStoreAuthorized(array $data): void;

    /**
     * Handle store installed/activated webhook
     */
    public function handleStoreInstalled(array $data): void;

    /**
     * Handle store uninstalled/deactivated webhook
     */
    public function handleStoreUninstalled(array $data): void;

    /**
     * Validate webhook signature/authenticity
     */
    public function validateWebhook(array $headers, string $payload): bool;
}
