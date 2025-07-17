<?php

namespace App\Repositories;

use App\Models\OrderItem;

class OrderItemRepo implements IRepository
{
    public function getAll(?int $limit): iterable
    {
        return $limit ? OrderItem::limit($limit)->get() : OrderItem::all();
    }

    public function getById(int|string $id): ?object
    {
        return OrderItem::findOrFail($id);
    }

    public function create(array $data): object
    {
        return OrderItem::create($data);
    }

    public function update(int|string $id, array $data): bool
    {
        $item = OrderItem::find($id);
        if (!$item) {
            return false;
        }
        return $item->update($data);
    }

    public function delete(int|string $id): bool
    {
        return OrderItem::find($id)->delete();
    }
}
