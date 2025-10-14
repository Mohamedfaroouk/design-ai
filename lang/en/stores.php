<?php

return [
    'title' => 'Store Integrations',
    'subtitle' => 'Manage connected stores',
    'add_new' => 'Connect New Store',
    'update_success' => 'Store updated successfully',
    'delete_success' => 'Store deleted successfully',
    'delete_confirm' => 'Are you sure you want to delete this store integration?',
    'delete_message' => 'This action cannot be undone. All data associated with this store will be permanently deleted.',
    'token_refresh_success' => 'Store token refreshed successfully',
    'token_refresh_failed' => 'Failed to refresh store token',

    'fields' => [
        'platform' => 'Platform',
        'merchant_id' => 'Merchant ID',
        'store_name' => 'Store Name',
        'domain' => 'Domain',
        'store_email' => 'Store Email',
        'store_phone' => 'Store Phone',
        'status' => 'Status',
        'token_expires_at' => 'Token Expires At',
        'user' => 'User',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
    ],

    'placeholders' => [
        'search' => 'Search stores...',
        'platform' => 'Select platform',
        'status' => 'Select status',
    ],

    'status' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'suspended' => 'Suspended',
    ],

    'platforms' => [
        'salla' => 'Salla',
        'zid' => 'Zid',
        'wordpress' => 'WordPress/WooCommerce',
    ],

    'actions' => [
        'refresh_token' => 'Refresh Token',
        'view_webhooks' => 'View Webhooks',
        'disconnect' => 'Disconnect',
    ],

    'validation' => [
        'status_required' => 'Status is required',
        'store_name_required' => 'Store name is required',
        'store_email_invalid' => 'Please enter a valid email address',
    ],

    // Salla specific
    'salla' => [
        'authorization_success' => 'Salla store authorized successfully',
        'authorization_failed' => 'Failed to authorize Salla store',
        'welcome_message' => 'Welcome! Your Salla store has been connected successfully.',
    ],

    // Zid specific
    'zid' => [
        'authorization_success' => 'Zid store authorized successfully',
        'authorization_failed' => 'Failed to authorize Zid store',
        'not_implemented' => 'Zid integration is not yet implemented',
    ],

    // WordPress specific
    'wordpress' => [
        'authorization_success' => 'WordPress store authorized successfully',
        'authorization_failed' => 'Failed to authorize WordPress store',
        'not_implemented' => 'WordPress integration is not yet implemented',
    ],
];
