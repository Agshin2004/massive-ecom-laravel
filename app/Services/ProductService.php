<?php

namespace App\Services;

use App\Models\User;
use App\Models\Product;
use App\DTOs\ProductDTO;
use App\Repositories\ProductRepo;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService
{
    public function __construct(
        private ProductRepo $productRepo,
    ) {}

    public function paginate(string $search = null, int $limit = null): LengthAwarePaginator
    {
        $user = auth()->user();
        $query = null;
        if ($user->isAdmin()) {
            $query = Product::query();
        }

        if ($user->isSeller()) {
            $query = $user->seller->products();
        }

        if ($search) {
            // grouped to keep the or clauses scoped together so it searches by name or description correctly
            $query->where(function ($q) use ($search) {
                $q
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->paginate($limit ?? 10);
    }

    public function findById(string $productId): Product
    {
        return Product::findOrFail($productId);
    }

    public function create(ProductDTO $dto, User $user): Product
    {
        // no need to have validation here since validation must be in controllers
        // if (! $user->isSeller() && ! $user->isAdmin()) {
        //     throw new ForbiddenException();
        // }

        sellerHasProduct($dto->name, $dto->price);

        $productData = [
            'name' => $dto->name,
            'description' => $dto->description,
            'price' => $dto->price,
            'category_id' => $dto->categoryId,
            'seller_id' => $dto->sellerId,
        ];

        return $this->productRepo->createForUser($productData, $user);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product;
    }

    public function destroy(string $productId)
    {
        return Product::findOrFail($productId)->delete();
    }

}
