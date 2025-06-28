<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Policies\ProductPolicy;
use Illuminate\Http\Request;
use App\Repositories\ProductRepo;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    public function __construct(private ProductRepo $repo)
    {
    }

    public function index(Request $request)
    {
        $products = $this->repo->getAllForUser($request->user());
        return $this->successResponse($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Product::class);

        $request->validate([
            'name' => ['required', 'min:3', 'max:120'],
            'description' => ['required'],
            'price' => ['required', 'numeric'],  // must be *.**
            'category_id' => ['required', 'exists:App\Models\Category,id'],
            'seller_id' => ['required', 'exists:App\Models\Seller,id']
        ]);

        if (auth()->user()->seller && $request->input('seller_id') != auth()->user()->seller->id) {
            return $this->errorResponse('You can only create products for your own seller account');
        }


        sellerHasProduct($request->input('name'), $request->input('price'));

        $productData = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'category_id' => $request->input('category_id'),
            'seller_id' => $request->input('seller_id')
        ];

        $product = $this->repo->createForUser($productData, $request->user());

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
            'seller_id' => ['prohibited']
        ]);

        $validated = $request->only([
            'name',
            'description',
            'price',
            'category_id'
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
