<?php

namespace App\Services\Admin;

use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    /**
     * Get all settings grouped
     */
    public function getAllSettings(array $filters = [])
    {
        $query = Setting::query();

        // Apply group filter
        if (isset($filters['group'])) {
            $query->where('group', $filters['group']);
        }

        // Apply search filter
        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('key', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->orderBy('group')->orderBy('key')->get()->groupBy('group');
    }

    /**
     * Get setting by key
     */
    public function getByKey(string $key): ?Setting
    {
        return Cache::remember("setting.{$key}", 3600, function () use ($key) {
            return Setting::where('key', $key)->first();
        });
    }

    /**
     * Get setting value by key
     */
    public function get(string $key, $default = null)
    {
        $setting = $this->getByKey($key);

        if (!$setting) {
            return $default;
        }

        return $setting->typed_value ?? $default;
    }

    /**
     * Update or create setting
     */
    public function set(string $key, $value, string $type = 'text', string $group = 'general', bool $isPublic = false): Setting
    {
        return DB::transaction(function () use ($key, $value, $type, $group, $isPublic) {
            $setting = Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'type' => $type,
                    'group' => $group,
                    'is_public' => $isPublic,
                ]
            );

            // Clear cache
            Cache::forget("setting.{$key}");

            return $setting;
        });
    }

    /**
     * Update multiple settings
     */
    public function updateBatch(array $settings): array
    {
        return DB::transaction(function () use ($settings) {
            $updated = [];

            foreach ($settings as $key => $value) {
                $setting = Setting::where('key', $key)->first();

                if ($setting) {
                    $setting->update(['value' => $value]);
                    Cache::forget("setting.{$key}");
                    $updated[] = $setting;
                }
            }

            return $updated;
        });
    }

    /**
     * Delete setting
     */
    public function delete(string $key): bool
    {
        return DB::transaction(function () use ($key) {
            $setting = Setting::where('key', $key)->first();

            if ($setting) {
                Cache::forget("setting.{$key}");
                return $setting->delete();
            }

            return false;
        });
    }

    /**
     * Get public settings (for frontend)
     */
    public function getPublicSettings(): array
    {
        return Cache::remember('settings.public', 3600, function () {
            return Setting::where('is_public', true)
                ->get()
                ->mapWithKeys(function ($setting) {
                    return [$setting->key => $setting->typed_value];
                })
                ->toArray();
        });
    }
}
