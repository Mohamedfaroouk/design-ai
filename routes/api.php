<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Users API routes (example - implement these controllers as needed)
Route::prefix('users')->group(function () {
    Route::get('/', function () {
        // Example response - replace with actual controller
        return response()->json([
            'data' => [
                [
                    'id' => 1,
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'role' => 'admin',
                    'status' => 'active',
                    'created_at' => now()->subDays(30)->toISOString()
                ],
                [
                    'id' => 2,
                    'name' => 'Jane Smith',
                    'email' => 'jane@example.com',
                    'role' => 'user',
                    'status' => 'active',
                    'created_at' => now()->subDays(15)->toISOString()
                ],
                [
                    'id' => 3,
                    'name' => 'Mike Johnson',
                    'email' => 'mike@example.com',
                    'role' => 'moderator',
                    'status' => 'inactive',
                    'created_at' => now()->subDays(7)->toISOString()
                ]
            ],
            'meta' => [
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 15,
                'total' => 3,
                'from' => 1,
                'to' => 3
            ]
        ]);
    });

    Route::get('/{id}', function ($id) {
        return response()->json([
            'data' => [
                'id' => $id,
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'role' => 'admin',
                'status' => 'active',
                'phone' => '+1234567890',
                'date_of_birth' => '1990-01-01',
                'avatar' => '',
                'bio' => 'Sample bio text',
                'created_at' => now()->subDays(30)->toISOString()
            ]
        ]);
    });

    Route::post('/', function (Request $request) {
        return response()->json([
            'message' => 'User created successfully',
            'data' => array_merge(['id' => rand(1000, 9999)], $request->all())
        ], 201);
    });

    Route::put('/{id}', function (Request $request, $id) {
        return response()->json([
            'message' => 'User updated successfully',
            'data' => array_merge(['id' => $id], $request->all())
        ]);
    });

    Route::delete('/{id}', function ($id) {
        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    });
});

// Upload endpoint (example)
Route::post('/upload/avatar', function (Request $request) {
    if ($request->hasFile('file')) {
        $path = $request->file('file')->store('avatars', 'public');
        return response()->json([
            'url' => asset('storage/' . $path),
            'path' => $path
        ]);
    }

    return response()->json(['error' => 'No file uploaded'], 400);
});
