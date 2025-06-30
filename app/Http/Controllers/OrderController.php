<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Repositories\OrderRepo;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private OrderRepo $repo)
    {
    }

    public function index()
    {
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $cartItems = $user->cart->items;

        $this->repo->createForUser($cartItems->toArray(), $user);

        return $this->noContent();
    }

    public function show(Order $order)
    {
        return $this->successResponse([
            // eager loading relationship befora so we can use whenLoaded on resource and safely fetch
            'order' => new OrderResource($order->load('orderItems.product')),
        ]);
    }

    public function update(Request $request, Order $order)
    {
        // what is update order tho? TODO: think of anything
    }

    public function destroy(Request $request, Order $order)
    {
        // cancelling order
    }
}
