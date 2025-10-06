<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions for settings
        Permission::firstOrCreate(['name' => 'settings.view']);
        Permission::firstOrCreate(['name' => 'settings.update']);

        // Assign permissions to admin role
        $adminRole = \Spatie\Permission\Models\Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo(['settings.view', 'settings.update']);
        }

        // Default settings
        $settings = [
            // General Settings
            [
                'key' => 'app.name',
                'value' => 'SaaS Dashboard',
                'type' => 'text',
                'group' => 'general',
                'is_public' => true,
            ],
            [
                'key' => 'app.description',
                'value' => 'Modern SaaS Dashboard Application',
                'type' => 'text',
                'group' => 'general',
                'is_public' => true,
            ],
            [
                'key' => 'app.logo',
                'value' => '/images/logo.png',
                'type' => 'image',
                'group' => 'general',
                'is_public' => true,
            ],
            [
                'key' => 'app.favicon',
                'value' => '/images/favicon.png',
                'type' => 'image',
                'group' => 'general',
                'is_public' => true,
            ],

            // Email Settings
            [
                'key' => 'mail.from.address',
                'value' => 'noreply@example.com',
                'type' => 'text',
                'group' => 'email',
                'is_public' => false,
            ],
            [
                'key' => 'mail.from.name',
                'value' => 'SaaS Dashboard',
                'type' => 'text',
                'group' => 'email',
                'is_public' => false,
            ],

            // Appearance Settings
            [
                'key' => 'appearance.primary_color',
                'value' => '#6366f1',
                'type' => 'text',
                'group' => 'appearance',
                'is_public' => true,
            ],
            [
                'key' => 'appearance.dark_mode_default',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'appearance',
                'is_public' => true,
            ],

            // Social Settings
            [
                'key' => 'social.facebook',
                'value' => '',
                'type' => 'text',
                'group' => 'social',
                'is_public' => true,
            ],
            [
                'key' => 'social.twitter',
                'value' => '',
                'type' => 'text',
                'group' => 'social',
                'is_public' => true,
            ],
            [
                'key' => 'social.instagram',
                'value' => '',
                'type' => 'text',
                'group' => 'social',
                'is_public' => true,
            ],
            [
                'key' => 'social.linkedin',
                'value' => '',
                'type' => 'text',
                'group' => 'social',
                'is_public' => true,
            ],

            // System Settings
            [
                'key' => 'system.maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'system',
                'is_public' => false,
            ],
            [
                'key' => 'system.allow_registration',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'system',
                'is_public' => true,
            ],
            [
                'key' => 'system.items_per_page',
                'value' => '15',
                'type' => 'number',
                'group' => 'system',
                'is_public' => false,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
