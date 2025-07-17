<?php

namespace App\Services;

use App\Repositories\OrderItemRepo;
use App\Services\Contracts\IService;

class OrderItemService implements IService
{
    public function __construct(
        private OrderItemRepo $orderItemRepo
    ) {}

    public function paginate(string $search = null, int $limit = null): LengthAwarePaginator {}

    public function findById(string|int $id): mixed {}

    public function create(mixed $dto, mixed $user): mixed {}
    public function update(mixed $model, array $data): mixed {}
    public function destroy(string|int $id): bool {}
}
