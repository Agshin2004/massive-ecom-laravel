<?php

namespace App\Http\Controllers\Shop;

use App\Models\Product;
use App\DTOs\ProductDTO;
use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Repositories\ProductRepo;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Exceptions\ForbiddenException;

class ProductController extends Controller
{
    public function __construct(private ProductService $productService) {}

    public function index(Request $request)
    {
        $products = $this->productService->paginate();

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

        $dto = new ProductDTO(
            name: $validated['name'],
            description: $validated['description'],
            price: $validated['price'],
            categoryId: $validated['category_id'],
            sellerId: $validated['seller_id']
        );

        $this->productService->create($dto, auth()->user());

        return $this->created();
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

        $validated = $request->validate([
            'name' => ['min:3', 'max:120'],
            'price' => ['numeric'],
            'category_id' => ['exists:App\Models\Category,id'],
            'seller_id' => ['prohibited'],
        ]);

        // checking some additional fields were passed
        if (count($validated) !== count($request->all())) {
            throw new ForbiddenException('Unexpected fields are in request!');
        }

        $this->productService->update($product, $validated);

        // didn't use repo since it is already looked up by laravel and no need to query again
        // $product->update($request->all());

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
