<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();

        return $this->successResponse(
            $categories
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'unique:App\Models\Category,name'],
        ]);

        Gate::authorize('create', Category::class);

        // if (Gate::denies('is-admin')) {
        //     throw new \Exception('Unauthorized', 400);
        // }

        $name = $request->input('name');
        $category = Category::create([
            'name' => $name,
        ]);

        return $this->successResponse([
            'category' => $category,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return $this->successResponse($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => ['required', 'unique:App\Models\Category,name'],
        ]);

        Gate::authorize('update', Category::class);

        $category->update([
            'name' => $request->input('name'),
        ]);

        return $this->successResponse($category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        Gate::authorize('delete', Category::class);

        $category->delete();

        return $this->noContent();
    }
}
