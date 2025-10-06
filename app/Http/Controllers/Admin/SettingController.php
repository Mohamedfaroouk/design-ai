<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingRequest;
use App\Http\Resources\Admin\SettingResource;
use App\Services\Admin\SettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    protected SettingService $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    /**
     * Display all settings grouped
     */
    public function index(Request $request): JsonResponse
    {
        $settings = $this->settingService->getAllSettings($request->all());

        return response()->json([
            'data' => SettingResource::collection($settings->flatten())->groupBy('group')
        ]);
    }

    /**
     * Update settings in batch
     */
    public function updateBatch(UpdateSettingRequest $request): JsonResponse
    {
        $updated = $this->settingService->updateBatch($request->validated()['settings']);

        return response()->json([
            'message' => __('settings.updated'),
            'data' => SettingResource::collection($updated)
        ]);
    }

    /**
     * Get public settings
     */
    public function public(): JsonResponse
    {
        $settings = $this->settingService->getPublicSettings();

        return response()->json([
            'data' => $settings
        ]);
    }
}
