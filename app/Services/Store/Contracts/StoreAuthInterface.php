<?php

namespace App\Services\Store\Contracts;

use App\Models\Store;
use App\Models\User;

interface StoreAuthInterface
{
    /**
     * Exchange authorization code for access token
     */
    public function getAccessToken(array $data): array;

    /**
     * Get user/store information using access token
     */
    public function getUserInfo(string $accessToken): array;

    /**
     * Handle OAuth authorization callback
     */
    public function handleAuthorization(array $validated): array;

    /**
     * Handle authorization webhook (for easy mode OAuth)
     */
    public function handleAuthorizationWebhook(array $data): void;

    /**
     * Refresh access token using refresh token
     */
    public function refreshToken(Store $store): bool;

    /**
     * Create or update user and store from OAuth data
     */
    public function createOrUpdateUser(array $userData, array $tokenData): User;
}
