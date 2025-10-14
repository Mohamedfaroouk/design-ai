<?php

namespace App\Services\Store\WordPress;

use App\Models\Store;
use App\Models\User;
use App\Services\Store\Contracts\StoreAuthInterface;

class WordPressAuthService implements StoreAuthInterface
{
    // TODO: Implement WordPress/WooCommerce OAuth flow
    // Reference: https://woocommerce.github.io/woocommerce-rest-api-docs/

    public function getAccessToken(array $data): array
    {
        throw new \Exception('WordPress integration not implemented yet');
    }

    public function getUserInfo(string $accessToken): array
    {
        throw new \Exception('WordPress integration not implemented yet');
    }

    public function handleAuthorization(array $validated): array
    {
        throw new \Exception('WordPress integration not implemented yet');
    }

    public function handleAuthorizationWebhook(array $data): void
    {
        throw new \Exception('WordPress integration not implemented yet');
    }

    public function refreshToken(Store $store): bool
    {
        throw new \Exception('WordPress integration not implemented yet');
    }

    public function createOrUpdateUser(array $userData, array $tokenData): User
    {
        throw new \Exception('WordPress integration not implemented yet');
    }
}
