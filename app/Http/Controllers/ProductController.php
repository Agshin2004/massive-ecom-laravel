<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        return $this->successResponse($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'min:3', 'max:120'],
            'description' => ['required'],
            'price' => ['required', 'numeric'],  // must be *.**
            'category_id' => ['required', 'exists:App\Models\Category,id'],
            'seller_id' => ['required', 'exists:App\Models\Seller,id']
        ]);

        sellerHasProduct($request->input('name'), $request->input('price'));

        $product = Product::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'category_id' => $request->input('category_id'),
            'seller_id' => $request->input('seller_id')
        ]);

        return $this->successResponse($product);
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
        if (empty($request->all()))
            return $this->errorResponse('At least one column needs to be changed');

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
        // additional checks
        if (count($validated) !== count($request->all()))
            abort(400, 'Unexpected fields are in request!');

        $product->update($request->all());

        return $this->successResponse($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return $this->noContent();
    }
}
