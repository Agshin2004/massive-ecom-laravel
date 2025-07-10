<?php

namespace App\Services;

use App\Exceptions\NotImplementedException;
use App\Models\User;
use App\Models\Product;
use App\Repositories\ProductRepo;
use App\Exceptions\ForbiddenException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

use function PHPUnit\Framework\never;

class ProductService
{
    public function __construct(
        private ProductRepo $productRepo,
        private string|null $sellerId, // will be validated before calling this service
        private string|null $categoryId, // will be validated before calling this service
    ) {}

    public static function make(string $sellerId = null, ?string $categoryId = null): self
    {
        return app()->make(self::class, [
            'sellerId' => $sellerId,
            'categoryId' => $categoryId,
        ]);
    }

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

    public function create(array $data, User $user): Product
    {
        if (! $user->isSeller() && ! $user->isAdmin()) {
            throw new ForbiddenException();
        }

        sellerHasProduct($data['name'], $data['price']);

        $productData = [
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'category_id' => $this->categoryId,
            'seller_id' => $this->sellerId,
        ];

        return $this->productRepo->createForUser($productData, $user);
    }

    public function update()
    {
        throw new NotImplementedException();
    }

    public function destroy(Product $product)
    {
        throw new NotImplementedException();
    }

}
