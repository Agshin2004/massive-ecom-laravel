<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {

    }

    public function store(Request $request)
    {
        $user = $request->user();
        $cartItems = $user->cart->items;

        DB::transaction(function () use ($user, $cartItems) {
            $order = Order::create([
                'user_id' => $user->id
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
            $order->orderItems()->createMany($cartItems->map(fn ($item) => [
                'product_id' => $item->product_id,
                'quantity' => $item->quantity
            ]));

            $user->cart()->delete();
        }, 3);

        
        return $this->noContent();
    }

    public function show(Order $order)
    {

    }

    public function update()
    {

    }

    public function destroy()
    {

    }
}
