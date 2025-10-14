<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    /**
     * Upload an image and return its public URL
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'image', 'max:5120'], // 5MB max
        ]);

        try {
            $file = $request->file('file');

            // Generate unique filename
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

            // Store in public/uploads directory
            $path = $file->storeAs('uploads', $filename, 'public');

            // Generate public URL
            $url = Storage::disk('public')->url($path);

            return response()->json([
                'message' => __('uploads.success'),
                'url' => $url,
                'path' => $path,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => __('uploads.failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
