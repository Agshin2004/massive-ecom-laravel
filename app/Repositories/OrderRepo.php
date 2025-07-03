<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\Product;
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

    public function createForUser(array $cartItems, User $user): array
    {
        return DB::transaction(function () use ($user, $cartItems) {
            // bulk fetching all products at once and index by id
            $products = Product::whereIn('id', collect($cartItems)->pluck('product_id'))
                ->get()
                ->keyBy('id'); // each key of product will be its id

            // group cart items by seller_id using the related products list
            // groupBy() takes each cart item, runs the callback, and uses the returned value as the group key
            // even though we don't return the full product, returning seller_id is enough for grouping
            // laravel internally builds a map like [seller_id => [items...]]
            $itemsGroupedBySeller = collect($cartItems)->groupBy(function ($item) use ($products) {
                $product = $products->get($item['product_id']);
                if ($product?->seller_id === null) {
                    throw new \RuntimeException('seller_id is null');
                }
                return $product?->seller_id;
            });

            $orders = [];

            foreach ($itemsGroupedBySeller as $sellerId => $items) {
                $order = Order::create([
                    'user_id' => $user->id,
                    'seller_id' => $sellerId,
                ]);

                $orderItems = $items->map(fn($item) => [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);

                // note: order_id is passed by createMany since we call it on relationship not model
                // this just creating orderItems for order, it will pass order_id to array returned rfom iterable
                $order->orderItems()->createMany($orderItems);
                $orders[] = $order;
            }

            $user->cart->items()->delete();
            return $orders;
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
