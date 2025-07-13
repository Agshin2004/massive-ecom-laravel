<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\CategoryRepo;
use Illuminate\Http\Request;

class AdminCategoriesController extends Controller
{
    public function __construct(
        private CategoryRepo $repo
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'limit' => ['numeric', 'min:1', 'max:100'],
        ]);
        $limit = $request->input('limit');
        $categories = $this->repo->getAll($limit);

        return $this->successResponse(
            compact('categories'),
        );
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
        $category = $this->repo->getById($id);
        return $this->successResponse(compact('category'));
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
        $this->repo->delete($id);
        return $this->noContent();
    }
}
