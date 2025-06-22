<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepo implements IRepository
{
    public function getAll(): iterable
    {
        return Product::all();
    }

    public function getById(int|string $id): ?object
    {
        return Product::find($id);
    }

    public function create(array $data): object
    {
        return Product::create($data);
    }

    public function update(int|string $id, array $data): bool
    {
        return Product::find($id)->update($data);
    }

    public function delete(int|string $id): bool
    {
        return Product::find($id)->delete();
    }
}
