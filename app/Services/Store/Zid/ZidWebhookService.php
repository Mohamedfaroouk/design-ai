<?php

namespace App\Services\Store\Zid;

use App\Services\Store\Contracts\StoreWebhookInterface;

class ZidWebhookService implements StoreWebhookInterface
{
    // TODO: Implement Zid webhook handlers
    // Reference: https://docs.zid.sa/

    public function handleStoreAuthorized(array $data): void
    {
        throw new \Exception('Zid integration not implemented yet');
    }

    public function handleStoreInstalled(array $data): void
    {
        throw new \Exception('Zid integration not implemented yet');
    }

    public function handleStoreUninstalled(array $data): void
    {
        throw new \Exception('Zid integration not implemented yet');
    }

    public function validateWebhook(array $headers, string $payload): bool
    {
        throw new \Exception('Zid integration not implemented yet');
    }
}
