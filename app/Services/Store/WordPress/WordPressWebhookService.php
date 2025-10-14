<?php

namespace App\Services\Store\WordPress;

use App\Services\Store\Contracts\StoreWebhookInterface;

class WordPressWebhookService implements StoreWebhookInterface
{
    // TODO: Implement WordPress/WooCommerce webhook handlers
    // Reference: https://woocommerce.github.io/woocommerce-rest-api-docs/

    public function handleStoreAuthorized(array $data): void
    {
        throw new \Exception('WordPress integration not implemented yet');
    }

    public function handleStoreInstalled(array $data): void
    {
        throw new \Exception('WordPress integration not implemented yet');
    }

    public function handleStoreUninstalled(array $data): void
    {
        throw new \Exception('WordPress integration not implemented yet');
    }

    public function validateWebhook(array $headers, string $payload): bool
    {
        throw new \Exception('WordPress integration not implemented yet');
    }
}
