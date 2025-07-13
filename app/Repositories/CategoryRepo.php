<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepo implements IRepository
{
    public function getAll(?int $limit = 10): iterable
    {
        return Category::paginate($limit);
    }

    public function getById(int|string $id): object|null
    {
        return Category::findOrFail($id);
    }

    public function create(array $data): object
    {
        return Category::create($data);
    }

    public function update(int|string $id, array $data): bool
    {
        return Category::findOrFail($id)->update($data);
    }

    public function delete(int|string $id): bool
    {
        return Category::findOrFail($id)->delete();
    }
}
