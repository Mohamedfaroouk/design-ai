<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingRequest;
use App\Http\Resources\Admin\SettingResource;
use App\Services\Admin\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WebSettingController extends Controller
{
    protected SettingService $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    /**
     * Display all settings grouped
     */
    public function index(Request $request): Response
    {
        $settings = $this->settingService->getAllSettings($request->all());

        // Transform grouped settings for Inertia
        // Resolve ResourceCollections to arrays
        $groupedSettings = [];
        foreach ($settings as $group => $items) {
            $collection = SettingResource::collection($items);
            $groupedSettings[$group] = $collection->toArray($request);
        }

        return Inertia::render('Modules/admin/Settings/SettingsIndex', [
            'settings' => $groupedSettings,
        ]);
    }

    /**
     * Update settings in batch
     */
    public function update(UpdateSettingRequest $request): RedirectResponse
    {
        try {
            $this->settingService->updateBatch($request->validated()['settings']);

            return redirect()->back()
                ->with('success', __('settings.updated'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
}
