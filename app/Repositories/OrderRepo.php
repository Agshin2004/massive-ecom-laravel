<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Order;
use App\Repositories\IRepository;

class OrderRepo implements IUserOwnedRepository
{
    public function getAllForUser(User $User): iterable
    {
        return Order::all();
    }

    public function findForUser(int|string $id, User $user): ?object
    {
        return Order::find($id);
    }

    public function createForUser(array $data, User $user): object
    {
        return Order::create($data);
    }

    public function updateForUser(int|string $id, array $data, User $user): bool
    {
        return Order::find($id)->update($data);
    }

    public function deleteForUser($id, User $user): bool
    {
        return Order::find($id)->delete();
    }
}
