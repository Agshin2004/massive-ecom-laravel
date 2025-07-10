<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Shop\ProductController;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminProductsController extends Controller
{
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

        return ProductService::make()->paginate($search, $limit);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return ProductService::make()->findById($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
