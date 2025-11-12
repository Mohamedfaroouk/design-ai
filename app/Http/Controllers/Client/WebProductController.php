<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreProductRequest;
use App\Http\Requests\Client\UpdateProductRequest;
use App\Http\Resources\Client\ProductResource;
use App\Models\Product;
use App\Services\Client\ProductService;
use App\Traits\HasDataTableInertia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WebProductController extends Controller
{
    use HasDataTableInertia;

    public function __construct(
        private ProductService $service
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        return $this->inertiaDataTable(
            page: 'Modules/client/Products/ProductsIndex',
            query: Product::where('user_id', auth()->id())
                ->withCount('images')
                ->with(['images' => function ($query) {
                    $query->latest()->limit(1);
                }]),
            request: $request,
            resource: ProductResource::class,
            searchable: ['name', 'description', 'sku'],
            filterable: ['platform'],
            defaultSort: 'created_at',
            defaultOrder: 'desc'
        );
    }

    /**
     * Show the form for creating a new product
     */
    public function create(): Response
    {
        return Inertia::render('Modules/client/Products/ProductsForm');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        try {
            $this->service->create($request->validated());

            return redirect()->route('client.products.index')
                ->with('success', __('messages.product.created'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): Response
    {
        // Ensure user owns the product
        if ($product->user_id !== auth()->id()) {
            abort(403, __('messages.unauthorized'));
        }

        $product->load(['images' => function ($query) {
            $query->latest();
        }]);

        return Inertia::render('Modules/client/Products/ProductDetail', [
            'product' => new ProductResource($product),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product): Response
    {
        // Ensure user owns the product
        if ($product->user_id !== auth()->id()) {
            abort(403, __('messages.unauthorized'));
        }

        return Inertia::render('Modules/client/Products/ProductsForm', [
            'product' => new ProductResource($product),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        // Ensure user owns the product
        if ($product->user_id !== auth()->id()) {
            abort(403, __('messages.unauthorized'));
        }

        try {
            $this->service->update($product, $request->validated());

            return redirect()->route('client.products.index')
                ->with('success', __('messages.product.updated'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        // Ensure user owns the product
        if ($product->user_id !== auth()->id()) {
            abort(403, __('messages.unauthorized'));
        }

        try {
            $this->service->delete($product);

            return redirect()->back()
                ->with('success', __('messages.product.deleted'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
}
