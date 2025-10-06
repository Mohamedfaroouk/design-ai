<?php

namespace App\Services;

use App\Models\User;
use App\Models\OtpVerification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthService
{
    /**
     * Attempt to login with email and password
     */
    public function login(string $email, string $password, bool $remember = false): array
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw new \Exception(__('auth.login.invalid_credentials'));
        }

        // Create Sanctum token
        $token = $user->createToken('auth-token')->plainTextToken;

        // Load user with roles, their permissions, and direct permissions
        $user->load(['roles.permissions', 'permissions']);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Send OTP for password reset
     */
    public function sendPasswordResetOtp(string $identifier, string $channel = 'email'): OtpVerification
    {
        // Check if user exists
        $user = User::where('email', $identifier)->first();

        if (!$user) {
            throw new \Exception(__('auth.forgot_password.user_not_found'));
        }

        // Invalidate previous OTPs
        OtpVerification::where('identifier', $identifier)
            ->where('type', 'password_reset')
            ->where('verified', false)
            ->update(['verified' => true]);

        // Generate 6-digit OTP
        $otp = $this->generateOtp();

        // Create OTP record
        $otpRecord = OtpVerification::create([
            'identifier' => $identifier,
            'channel' => $channel,
            'otp' => $otp,
            'type' => 'password_reset',
            'expires_at' => Carbon::now()->addMinutes(10),
            'attempts' => 0,
            'verified' => false,
        ]);

        // Send OTP via email or SMS
        if ($channel === 'email') {
            $this->sendOtpEmail($identifier, $otp);
        } else {
            // TODO: Implement SMS sending
            throw new \Exception(__('auth.sms_not_implemented'));
        }

        return $otpRecord;
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(string $identifier, string $otp, string $type = 'password_reset'): bool
    {
        $otpRecord = OtpVerification::where('identifier', $identifier)
            ->where('type', $type)
            ->where('verified', false)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$otpRecord) {
            throw new \Exception(__('auth.otp.not_found'));
        }

        // Check if expired
        if ($otpRecord->isExpired()) {
            throw new \Exception(__('auth.otp.expired'));
        }

        // Check max attempts
        if ($otpRecord->attempts >= 3) {
            throw new \Exception(__('auth.otp.max_attempts'));
        }

        // Increment attempts
        $otpRecord->incrementAttempts();

        // Check if OTP matches
        if ($otpRecord->otp !== $otp) {
            throw new \Exception(__('auth.otp.invalid'));
        }

        // Mark as verified
        $otpRecord->markAsVerified();

        return true;
    }

    /**
     * Reset password with verified OTP
     */
    public function resetPassword(string $identifier, string $password): bool
    {
        // Check if OTP was verified
        $otpRecord = OtpVerification::where('identifier', $identifier)
            ->where('type', 'password_reset')
            ->where('verified', true)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$otpRecord) {
            throw new \Exception(__('auth.otp.verification_required'));
        }

        // Check if OTP is still valid (within 30 minutes of verification)
        if ($otpRecord->updated_at->diffInMinutes(Carbon::now()) > 30) {
            throw new \Exception(__('auth.otp.verification_expired'));
        }

        // Update user password
        $user = User::where('email', $identifier)->first();

        if (!$user) {
            throw new \Exception(__('auth.reset_password.user_not_found'));
        }

        $user->update([
            'password' => Hash::make($password),
        ]);

        // Revoke all user tokens
        $user->tokens()->delete();

        return true;
    }

    /**
     * Resend OTP
     */
    public function resendOtp(string $identifier, string $type = 'password_reset', string $channel = 'email'): OtpVerification
    {
        return $this->sendPasswordResetOtp($identifier, $channel);
    }

    /**
     * Logout user
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    /**
     * Generate 6-digit OTP
     */
    protected function generateOtp(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Send OTP via email
     */
    protected function sendOtpEmail(string $email, string $otp): void
    {
        // TODO: Create a proper Mailable class
        // For now, using basic mail
        Mail::raw("Your OTP code is: {$otp}\n\nThis code will expire in 10 minutes.", function ($message) use ($email) {
            $message->to($email)
                ->subject('Password Reset OTP');
        });
    }
}
