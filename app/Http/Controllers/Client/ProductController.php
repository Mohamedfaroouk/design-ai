<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreProductRequest;
use App\Http\Requests\Client\UpdateProductRequest;
use App\Http\Resources\Client\ProductResource;
use App\Models\Product;
use App\Services\Client\ProductService;
use App\Traits\HasDataTable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use HasDataTable;

    public function __construct(
        private ProductService $service
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        return $this->dataTableResponse(
            query: Product::where('user_id', auth()->id())
                ->withCount('images')
                ->with(['images' => function ($query) {
                    $query->latest()->limit(1);
                }]),
            request: $request,
            resource: ProductResource::class,
            searchable: ['name', 'description', 'sku'],
            filterable: ['platform']
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->service->create($request->validated());

        return response()->json([
            'data' => new ProductResource($product),
            'message' => __('messages.product.created'),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): JsonResponse
    {
        // Ensure user owns the product
        if ($product->user_id !== auth()->id()) {
            abort(403, __('messages.unauthorized'));
        }

        $product->load(['images' => function ($query) {
            $query->latest();
        }]);

        return response()->json([
            'data' => new ProductResource($product),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        // Ensure user owns the product
        if ($product->user_id !== auth()->id()) {
            abort(403, __('messages.unauthorized'));
        }

        $product = $this->service->update($product, $request->validated());

        return response()->json([
            'data' => new ProductResource($product),
            'message' => __('messages.product.updated'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): JsonResponse
    {
        // Ensure user owns the product
        if ($product->user_id !== auth()->id()) {
            abort(403, __('messages.unauthorized'));
        }

        $this->service->delete($product);

        return response()->json([
            'message' => __('messages.product.deleted'),
        ]);
    }
}
