<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Store Integration Services
    |--------------------------------------------------------------------------
    */

    'salla' => [
        'client_id' => env('SALLA_CLIENT_ID'),
        'client_secret' => env('SALLA_CLIENT_SECRET'),
        'redirect_uri' => env('SALLA_REDIRECT_URI', env('APP_URL') . '/api/integration/salla/callback'),
    ],

    'zid' => [
        'client_id' => env('ZID_CLIENT_ID'),
        'client_secret' => env('ZID_CLIENT_SECRET'),
        'redirect_uri' => env('ZID_REDIRECT_URI', env('APP_URL') . '/api/integration/zid/callback'),
    ],

    'wordpress' => [
        'client_id' => env('WORDPRESS_CLIENT_ID'),
        'client_secret' => env('WORDPRESS_CLIENT_SECRET'),
        'redirect_uri' => env('WORDPRESS_REDIRECT_URI', env('APP_URL') . '/api/integration/wordpress/callback'),
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Image Generation Service
    |--------------------------------------------------------------------------
    */

    'kie_ai' => [
        'api_key' => env('KIE_AI_API_KEY'),
        'api_url' => env('KIE_AI_API_URL', 'https://api.kie.ai/api/v1'),
    ],

];
