<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Login user
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login(
                $request->input('email'),
                $request->input('password'),
                $request->input('remember', false)
            );

            return response()->json([
                'success' => true,
                'message' => __('auth.login.success'),
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 401);
        }
    }

    /**
     * Send password reset OTP
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            $this->authService->sendPasswordResetOtp(
                $request->input('email'),
                $request->input('channel', 'email')
            );

            return response()->json([
                'success' => true,
                'message' => __('auth.otp.sent'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        try {
            $this->authService->verifyOtp(
                $request->input('email'),
                $request->input('otp'),
                $request->input('type', 'password_reset')
            );

            return response()->json([
                'success' => true,
                'message' => __('auth.otp.verified'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Reset password
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $this->authService->resetPassword(
                $request->input('email'),
                $request->input('password')
            );

            return response()->json([
                'success' => true,
                'message' => __('auth.reset_password.success'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'type' => 'sometimes|string|in:password_reset,email_verification',
            'channel' => 'sometimes|string|in:email,phone',
        ]);

        try {
            $this->authService->resendOtp(
                $request->input('email'),
                $request->input('type', 'password_reset'),
                $request->input('channel', 'email')
            );

            return response()->json([
                'success' => true,
                'message' => __('auth.otp.resent'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get authenticated user
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load(['roles.permissions', 'permissions']);

        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json([
            'success' => true,
            'message' => __('auth.logout.success'),
        ]);
    }
}
