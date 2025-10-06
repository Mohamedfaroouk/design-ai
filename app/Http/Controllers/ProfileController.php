<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\Admin\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Get the authenticated user's profile
     */
    public function show(): JsonResponse
    {
        $user = auth()->user()->load('roles.permissions');

        return response()->json([
            'data' => new UserResource($user)
        ]);
    }

    /**
     * Update the authenticated user's profile
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = auth()->user();

        $user->update($request->validated());

        return response()->json([
            'message' => __('profile.updated'),
            'data' => new UserResource($user->fresh())
        ]);
    }

    /**
     * Change the authenticated user's password
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = auth()->user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => __('profile.password.current_incorrect'),
                'errors' => [
                    'current_password' => [__('profile.password.current_incorrect')]
                ]
            ], 422);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Revoke all tokens except current one
        $currentToken = $user->currentAccessToken();
        $user->tokens()->where('id', '!=', $currentToken->id)->delete();

        return response()->json([
            'message' => __('profile.password.changed')
        ]);
    }
}
