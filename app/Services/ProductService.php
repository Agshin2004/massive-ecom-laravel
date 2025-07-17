<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepo;
use App\Services\Contracts\IService;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService implements IService
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

    public function findById(string|int $productId): Product
    {
        return Product::findOrFail($productId);
    }

    public function create(mixed $dto, mixed $user): mixed
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

    public function update(mixed $product, array $data): mixed
    {
        $product->update($data);

        return $product;
    }

    public function destroy(string|int $productId): bool
    {
        return Product::findOrFail($productId)->delete();
    }

}
