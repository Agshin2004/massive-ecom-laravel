<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Repositories\ProductRepo;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    // public function __construct(private ProductService $productService) {}

    public function index(Request $request)
    {
        $products = ProductService::make()->allProducts();

        return $this->successResponse($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Product::class);

        $validated = $request->validate([
            'name' => ['required', 'min:3', 'max:120'],
            'description' => ['required'],
            'price' => ['required', 'numeric'],  // must be *.**
            'category_id' => ['required', 'exists:App\Models\Category,id'],
            'seller_id' => ['required', 'exists:App\Models\Seller,id'],
        ]);

        $product = ProductService::make($validated['seller_id'], $validated['category_id'])
            ->createProduct($validated, auth()->user());

        return $this->successResponse(['product' => $product]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return $this->successResponse($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        Gate::authorize('update', $product);

        if (empty($request->all())) {
            return $this->errorResponse('At least one column needs to be changed');
        }

        $request->validate([
            'name' => ['min:3', 'max:120'],
            'price' => ['numeric'],
            'category_id' => ['exists:App\Models\Category,id'],
            'seller_id' => ['prohibited'],
        ]);

        $validated = $request->only([
            'name',
            'description',
            'price',
            'category_id',
        ]);

        // checking some additional fields were passed
        if (count($validated) !== count($request->all())) {
            abort(400, 'Unexpected fields are in request!');
        }

        // didn't use repo since it is already looked up by laravel
        $product->update($request->all());

        return $this->successResponse($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        Gate::authorize('delete', $product);

        $product->delete();

        return $this->noContent();
    }
}
