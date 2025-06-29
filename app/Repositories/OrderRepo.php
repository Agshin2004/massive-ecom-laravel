<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderRepo implements IUserOwnedRepository
{
    public function getAllForUser(User $User): iterable
    {
        return Order::all();
    }

    public function findForUser(int|string $id, User $user): ?Order
    {
        return Order::find($id);
    }

    public function createForUser(array $cartItems, User $user): Order
    {
        return DB::transaction(function () use ($user, $cartItems) {
            $order = Order::create([
                'user_id' => $user->id,
            ]);

            // foreach ($cartItems as $item) {
            //     OrderItem::create([
            //         'order_id' => $order->id,
            //         'product_id' => $item->product_id,
            //         'quantity' => $item->quantity
            //     ]);
            // }

            // note: order_id is passed by createMany since we call it on relationship not model
            // this just creating orderItems for order, it will pass order_id to array returned rfom iterable
            $order->orderItems()->createMany(collect($cartItems)->map(fn ($item) => [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
            ]));

            $user->cart->items()->delete();

            return $order;
        }, 3);
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
