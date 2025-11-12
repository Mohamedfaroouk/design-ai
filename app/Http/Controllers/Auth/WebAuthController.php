<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class WebAuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Show login form
     */
    public function showLogin(): Response
    {
        return Inertia::render('auth/Login');
    }

    /**
     * Login user
     */
    public function login(LoginRequest $request): RedirectResponse
    {

        try {
            // Use Laravel's standard auth attempt
            $credentials = $request->only('email', 'password');
            $remember = $request->boolean('remember', false);

            if (Auth::attempt($credentials, $remember)) {
                $request->session()->regenerate();
                
                // Ensure session is saved before redirecting
                $request->session()->save();

                // Determine redirect based on user role
                $user = Auth::user();
                $role = $user->getRoleNames()->first() ?? null;

                $redirectUrl = match($role) {
                    'admin' => route('admin.dashboard'),
                    'client' => route('client.dashboard'),
                    default => route('dashboard'),
                };
                
                return redirect($redirectUrl)
                    ->with('success', __('auth.login.success'));
            }

            return back()->withErrors([
                'email' => __('auth.login.invalid_credentials'),
            ])->onlyInput('email');

        } catch (\Exception $e) {

            return back()->withErrors([
                'email' => $e->getMessage(),
            ])->onlyInput('email');
        }
    }

    /**
     * Show forgot password form
     */
    public function showForgotPassword(): Response
    {
        return Inertia::render('auth/ForgotPassword');
    }

    /**
     * Send password reset OTP
     */
    public function forgotPassword(ForgotPasswordRequest $request): RedirectResponse
    {
        try {
            $this->authService->sendPasswordResetOtp(
                $request->input('email'),
                $request->input('channel', 'email')
            );

            return redirect()->route('auth.otp.verify', ['email' => $request->input('email')])
                ->with('success', __('auth.otp.sent'));

        } catch (\Exception $e) {
            return back()->withErrors([
                'email' => $e->getMessage(),
            ])->onlyInput('email');
        }
    }

    /**
     * Show OTP verification form
     */
    public function showVerifyOtp(Request $request): Response
    {
        return Inertia::render('auth/OtpVerification', [
            'email' => $request->query('email'),
        ]);
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(VerifyOtpRequest $request): RedirectResponse
    {
        try {
            $this->authService->verifyOtp(
                $request->input('email'),
                $request->input('otp'),
                $request->input('type', 'password_reset')
            );

            return redirect()->route('auth.password.reset', ['email' => $request->input('email')])
                ->with('success', __('auth.otp.verified'));

        } catch (\Exception $e) {
            return back()->withErrors([
                'otp' => $e->getMessage(),
            ])->withInput();
        }
    }

    /**
     * Show reset password form
     */
    public function showResetPassword(Request $request): Response
    {
        return Inertia::render('auth/ResetPassword', [
            'email' => $request->query('email'),
        ]);
    }

    /**
     * Reset password
     */
    public function resetPassword(ResetPasswordRequest $request): RedirectResponse
    {
        try {
            $this->authService->resetPassword(
                $request->input('email'),
                $request->input('password')
            );

            return redirect()->route('login')
                ->with('success', __('auth.reset_password.success'));

        } catch (\Exception $e) {
            return back()->withErrors([
                'password' => $e->getMessage(),
            ])->withInput(['email']);
        }
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request): RedirectResponse
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

            return back()->with('success', __('auth.otp.resent'));

        } catch (\Exception $e) {
            return back()->withErrors([
                'email' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Logout user
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', __('auth.logout.success'));
    }
}
