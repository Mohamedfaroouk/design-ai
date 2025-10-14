<?php

namespace App\Services\Store\Zid;

use App\Models\Store;
use App\Models\User;
use App\Services\Store\Contracts\StoreAuthInterface;

class ZidAuthService implements StoreAuthInterface
{
    // TODO: Implement Zid OAuth flow
    // Reference: https://docs.zid.sa/

    public function getAccessToken(array $data): array
    {
        throw new \Exception('Zid integration not implemented yet');
    }

    public function getUserInfo(string $accessToken): array
    {
        throw new \Exception('Zid integration not implemented yet');
    }

    public function handleAuthorization(array $validated): array
    {
        throw new \Exception('Zid integration not implemented yet');
    }

    public function handleAuthorizationWebhook(array $data): void
    {
        throw new \Exception('Zid integration not implemented yet');
    }

    public function refreshToken(Store $store): bool
    {
        throw new \Exception('Zid integration not implemented yet');
    }

    public function createOrUpdateUser(array $userData, array $tokenData): User
    {
        throw new \Exception('Zid integration not implemented yet');
    }
}
