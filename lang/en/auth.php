<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user.
    |
    */

    'login' => [
        'success' => 'Login successful',
        'invalid_credentials' => 'Invalid credentials',
        'user_not_found' => 'User not found',
    ],

    'forgot_password' => [
        'otp_sent' => 'OTP sent successfully',
        'user_not_found' => 'User not found',
    ],

    'otp' => [
        'sent' => 'OTP sent successfully',
        'resent' => 'OTP resent successfully',
        'verified' => 'OTP verified successfully',
        'invalid' => 'Invalid OTP',
        'expired' => 'OTP has expired',
        'not_found' => 'OTP not found',
        'max_attempts' => 'Maximum verification attempts exceeded',
        'verification_required' => 'OTP verification required',
        'verification_expired' => 'OTP verification expired',
    ],

    'reset_password' => [
        'success' => 'Password reset successfully',
        'user_not_found' => 'User not found',
    ],

    'logout' => [
        'success' => 'Logged out successfully',
    ],

    'sms_not_implemented' => 'SMS channel not implemented yet',
];
