<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Packages Language Lines
    |--------------------------------------------------------------------------
    */

    'created' => 'Package created successfully',
    'updated' => 'Package updated successfully',
    'deleted' => 'Package deleted successfully',
    'not_found' => 'Package not found',
    'has_active_subscriptions' => 'Cannot delete package with active subscriptions',

    'fields' => [
        'name' => 'Package Name',
        'display_name' => 'Display Name',
        'description' => 'Description',
        'platform' => 'Platform',
        'price' => 'Price',
        'currency' => 'Currency',
        'billing_cycle' => 'Billing Cycle',
        'photos_limit' => 'Photos Limit',
        'features' => 'Features',
        'is_active' => 'Active',
        'is_featured' => 'Featured',
        'sort_order' => 'Sort Order',
    ],

    'billing_cycles' => [
        'monthly' => 'Monthly',
        'yearly' => 'Yearly',
        'lifetime' => 'Lifetime',
    ],

    'platforms' => [
        'all' => 'All Platforms',
        'salla' => 'Salla',
        'zid' => 'Zid',
        'wordpress' => 'WordPress',
    ],

    'unlimited' => 'Unlimited',
    'photos' => '{count} Photos',
];
