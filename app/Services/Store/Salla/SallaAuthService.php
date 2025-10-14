<?php

namespace App\Services\Store\Salla;

use App\Enums\PlatformType;
use App\Enums\StoreStatus;
use App\Models\Store;
use App\Models\User;
use App\Services\Store\Contracts\StoreAuthInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class SallaAuthService implements StoreAuthInterface
{
    protected string $tokenUrl = 'https://accounts.salla.sa/oauth2/token';
    protected string $userInfoUrl = 'https://accounts.salla.sa/oauth2/user/info';

    public function getAccessToken(array $data): array
    {
        $response = Http::asForm()->post($this->tokenUrl, [
            ...$data,
            'grant_type' => 'authorization_code',
            'client_id' => config('services.salla.client_id'),
            'client_secret' => config('services.salla.client_secret'),
            'redirect_uri' => config('services.salla.redirect_uri'),
        ]);

        return $response->throw()->json();
    }

    public function getUserInfo(string $accessToken): array
    {
        $response = Http::withToken($accessToken)
            ->get($this->userInfoUrl);

        return $response->throw()->json();
    }

    public function handleAuthorization(array $validated): array
    {
        try {
            $token = $this->getAccessToken($validated);
            $userData = $this->getUserInfo($token['access_token']);

            $user = $this->createOrUpdateUser($userData['data'], $token);

            return [
                'user' => $user,
                'store' => $user->stores()->where('platform', PlatformType::SALLA)->first()
            ];
        } catch (Exception $e) {
            Log::error('Salla authorization error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function handleAuthorizationWebhook(array $data): void
    {
        try {
            $token = $data['data'];
            $userData = $this->getUserInfo($token['access_token']);

            $this->createOrUpdateUser($userData['data'], $token);
        } catch (Exception $e) {
            Log::error('Salla authorization webhook error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function refreshToken(Store $store): bool
    {
        try {
            $response = Http::asForm()->post($this->tokenUrl, [
                'client_id' => config('services.salla.client_id'),
                'client_secret' => config('services.salla.client_secret'),
                'grant_type' => 'refresh_token',
                'refresh_token' => $store->refresh_token,
            ]);

            if (!$response->successful()) {
                throw new Exception('Failed to refresh token: ' . $response->body());
            }

            $data = $response->json();

            $store->update([
                'access_token' => $data['access_token'],
                'refresh_token' => $data['refresh_token'],
                'token_expires_at' => now()->addSeconds($data['expires_in']),
            ]);

            Log::info('Successfully refreshed Salla token for store: ' . $store->id);

            return true;
        } catch (Exception $e) {
            Log::error('Failed to refresh Salla token for store: ' . $store->id, [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function createOrUpdateUser(array $userData, array $tokenData): User
    {
        return DB::transaction(function () use ($userData, $tokenData) {
            $merchantId = $userData['merchant']['id'];

            // Check if store exists using merchant_id
            $store = Store::where('merchant_id', $merchantId)
                         ->where('platform', PlatformType::SALLA)
                         ->first();

            if ($store) {
                // Update existing store and user
                $user = $store->user;

                $this->updateStore($store, $userData, $tokenData);
                $this->updateUser($user, $userData);

                return $user;
            }

            // Check if user exists by email
            $user = User::where('email', $userData['email'])->first();

            if (!$user) {
                // Create new user
                $user = $this->createUser($userData);
            }

            // Create new store for existing or new user
            $this->createStore($user, $userData, $tokenData);

            return $user;
        });
    }

    protected function createUser(array $userData): User
    {
        $password = rand(100000, 999999);

        $user = User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);

        // Assign default client role
        $user->assignRole('client');

        // TODO: Send welcome email with password
        // Mail::to($user->email)->send(new WelcomeEmail($user, $password));

        return $user;
    }

    protected function updateUser(User $user, array $userData): void
    {
        $user->update([
            'name' => $userData['name'],
            'email' => $userData['email'],
        ]);
    }

    protected function createStore(User $user, array $userData, array $tokenData): Store
    {
        return Store::create([
            'user_id' => $user->id,
            'platform' => PlatformType::SALLA,
            'merchant_id' => $userData['merchant']['id'],
            'store_id' => $userData['id'],
            'domain' => $userData['merchant']['domain'],
            'store_name' => $userData['merchant']['name'],
            'store_email' => $userData['email'],
            'store_phone' => $userData['mobile'] ?? null,
            'avatar' => $userData['merchant']['avatar'] ?? null,
            'access_token' => $tokenData['access_token'],
            'refresh_token' => $tokenData['refresh_token'],
            'token_expires_at' => now()->addSeconds($tokenData['expires_in'] ?? $tokenData['expires']),
            'status' => StoreStatus::ACTIVE,
            'metadata' => [
                'merchant' => $userData['merchant'],
            ],
        ]);
    }

    protected function updateStore(Store $store, array $userData, array $tokenData): void
    {
        $store->update([
            'access_token' => $tokenData['access_token'],
            'refresh_token' => $tokenData['refresh_token'],
            'token_expires_at' => now()->addSeconds($tokenData['expires_in'] ?? $tokenData['expires']),
            'domain' => $userData['merchant']['domain'],
            'store_name' => $userData['merchant']['name'],
            'store_email' => $userData['email'],
            'store_phone' => $userData['mobile'] ?? null,
            'avatar' => $userData['merchant']['avatar'] ?? null,
            'status' => StoreStatus::ACTIVE,
        ]);
    }
}
