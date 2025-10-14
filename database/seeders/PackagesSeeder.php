<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            // Salla Packages
            [
                'name' => 'salla_basic_monthly',
                'display_name' => 'Basic Plan',
                'description' => 'Perfect for small stores getting started',
                'platform' => 'salla',
                'price' => 99.00,
                'currency' => 'SAR',
                'billing_cycle' => 'monthly',
                'photos_limit' => 100,
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 1,
                'features' => [
                    '100 photos per month',
                    'AI-powered background removal',
                    'Basic image editing',
                    'Email support',
                ],
                'metadata' => [
                    'recommended' => false,
                ],
            ],
            [
                'name' => 'salla_professional_monthly',
                'display_name' => 'Professional Plan',
                'description' => 'Best for growing businesses',
                'platform' => 'salla',
                'price' => 299.00,
                'currency' => 'SAR',
                'billing_cycle' => 'monthly',
                'photos_limit' => 500,
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 2,
                'features' => [
                    '500 photos per month',
                    'AI-powered background removal',
                    'Advanced image editing',
                    'Bulk processing',
                    'Priority support',
                ],
                'metadata' => [
                    'recommended' => true,
                    'popular' => true,
                ],
            ],
            [
                'name' => 'salla_enterprise_monthly',
                'display_name' => 'Enterprise Plan',
                'description' => 'For large-scale operations',
                'platform' => 'salla',
                'price' => 799.00,
                'currency' => 'SAR',
                'billing_cycle' => 'monthly',
                'photos_limit' => 0, // Unlimited
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 3,
                'features' => [
                    'Unlimited photos',
                    'AI-powered background removal',
                    'Advanced image editing',
                    'Bulk processing',
                    'Priority support',
                    'API access',
                    'Custom integrations',
                ],
                'metadata' => [
                    'recommended' => false,
                ],
            ],

            // Free Trial Package (All Platforms)
            [
                'name' => 'free_trial',
                'display_name' => 'Free Trial',
                'description' => 'Try our service for free',
                'platform' => 'all',
                'price' => 0.00,
                'currency' => 'SAR',
                'billing_cycle' => 'monthly',
                'photos_limit' => 10,
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 0,
                'features' => [
                    '10 photos to try',
                    'AI-powered background removal',
                    'Basic image editing',
                ],
                'metadata' => [
                    'trial' => true,
                ],
            ],

            // Zid Packages (for future use)
            [
                'name' => 'zid_basic_monthly',
                'display_name' => 'Zid Basic Plan',
                'description' => 'Perfect for Zid store owners',
                'platform' => 'zid',
                'price' => 99.00,
                'currency' => 'SAR',
                'billing_cycle' => 'monthly',
                'photos_limit' => 100,
                'is_active' => false, // Not active yet
                'is_featured' => false,
                'sort_order' => 10,
                'features' => [
                    '100 photos per month',
                    'AI-powered background removal',
                    'Basic image editing',
                ],
                'metadata' => [],
            ],

            // WordPress Packages (for future use)
            [
                'name' => 'wordpress_basic_monthly',
                'display_name' => 'WordPress Basic Plan',
                'description' => 'Perfect for WordPress stores',
                'platform' => 'wordpress',
                'price' => 99.00,
                'currency' => 'USD',
                'billing_cycle' => 'monthly',
                'photos_limit' => 100,
                'is_active' => false, // Not active yet
                'is_featured' => false,
                'sort_order' => 20,
                'features' => [
                    '100 photos per month',
                    'AI-powered background removal',
                    'Basic image editing',
                ],
                'metadata' => [],
            ],
        ];

        foreach ($packages as $package) {
            Package::create($package);
        }

        $this->command->info('Packages seeded successfully!');
    }
}
