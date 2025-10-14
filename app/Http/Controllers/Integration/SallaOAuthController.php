<?php

namespace App\Http\Controllers\Integration;

use App\Http\Controllers\Controller;
use App\Services\Store\Salla\SallaAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class SallaOAuthController extends Controller
{
    public function __construct(
        protected SallaAuthService $sallaAuthService
    ) {
    }

    /**
     * Redirect to Salla authorization page
     */
    public function redirect(): JsonResponse
    {
        $clientId = config('services.salla.client_id');
        $redirectUri = config('services.salla.redirect_uri');

        $authUrl = "https://accounts.salla.sa/oauth2/authorize?" . http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'offline_access',
        ]);

        return response()->json([
            'authorization_url' => $authUrl
        ]);
    }

    /**
     * Handle OAuth callback from Salla
     */
    public function callback(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        try {
            $result = $this->sallaAuthService->handleAuthorization([
                'code' => $request->code,
            ]);

            $user = $result['user'];
            $store = $result['store'];

            // Generate password reset token for first-time users
            $resetToken = Password::broker('users')->createToken($user);

            // Generate Sanctum token for API authentication
            $token = $user->createToken('store-integration')->plainTextToken;

            return response()->json([
                'message' => __('stores.salla.authorization_success'),
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'store' => [
                        'id' => $store->id,
                        'platform' => $store->platform->value,
                        'domain' => $store->domain,
                        'store_name' => $store->store_name,
                    ],
                    'token' => $token,
                    'reset_url' => url(route('password.reset', ['token' => $resetToken, 'email' => $user->email], false)),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Salla OAuth callback error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => __('stores.salla.authorization_failed'),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
