<?php

namespace App\Http\Controllers\Shop;

use App\Enums\OrderStatus;
use App\Exceptions\NotImplementedException;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Repositories\OrderRepo;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private OrderRepo $repo) {}

    public function index() {}

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
        throw new NotImplementedException('UPDATE FOR ORDERS NOT IMPLEMENTED');
    }

    public function destroy(Order $order)
    {
        // cancelling order
        $order->is_active = false;
        $order->order_status = OrderStatus::CANCELED_BY_USER->value;
        // decided not to delete orderItems, it can be deleted if needed tho
        // thing is that if the clients wants to list all non active or cancelled
        // orders, client app can list it, if it was deleted, there wouldn't be that feat
        // $order->orderItems()->delete();
        $order->save();

        return $this->noContent();
    }
}
