<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Product;

class ProductRepo implements IUserOwnedRepository
{
    public function getAllForUser(User $user): iterable
    {
        return $user->products;
    }

    public function findForUser(int|string $id, User $user): ?Product
    {
        return Product::where('id', $id)->where('user_id', $user->id)->first();
    }

    public function createForUser(array $data, User $user): Product
    {
        // if ($user->isAdmin()) {
        //     return Product::create($data);
        // }
        // return $user->seller->products()->create($data);

        return Product::create($data);
    }

    public function updateForUser(int|string $id, array $data, User $user): bool
    {
        $product = Product::where('id', $id)->where('user_id', $user->id)->first();

        return $product ? $product->update($data) : false;
    }

    public function deleteForUser(int|string $id, User $user): bool
    {
        $product = Product::where('id', $id)->where('user_id', $user->id)->first();

        return $product ? $product->delete() : false;
    }
}
