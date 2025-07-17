<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\DTOs\ProductDTO;
use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Http\Controllers\Controller;
use App\Exceptions\ForbiddenException;
use App\Http\Controllers\Shop\ProductController;

class AdminProductsController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'limit' => ['sometimes', 'min:1', 'max:100', 'integer'],
        ]);

        $limit = $request->input('limit') ?? 10;
        $search = $request->input('search') ?? null;

        $paginatedProducts = $this->productService->paginate($search, $limit);
        return $this->successResponse([
            'products' => $paginatedProducts,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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
    public function show(string $id)
    {
        $product = $this->productService->findById($id);

        return $this->successResponse(['product' => $product]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // NOTE: didn't use Gate here since this controller already makes use of isAdmin middleware to check if user is amdin

        $validated = $request->validate([
            'name' => ['min:3', 'max:120'],
            'price' => ['numeric'],
            'category_id' => ['exists:App\Models\Category,id'],
            'seller_id' => ['prohibited'],
        ]);

        if (count($validated) !== count($request->all())) {
            throw new ForbiddenException('Unexpected fields are in request!');
        }

        $product = $this->productService->update(Product::findOrFail($id), $validated);

        return $this->successResponse(['product' => $product]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->productService->destroy($id);
        return $this->noContent();
    }
}
