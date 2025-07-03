<?php

namespace App\Http\Controllers\Seller;

use App\Models\Order;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class SellerController extends Controller
{
    public function updateOrderStatus(Request $request)
    {
        $request->validate([
            'order_status' => ['required', Rule::in(OrderStatus::values())],
            'order_id' => ['required', 'exists:App\Models\Order,id'],
        ]);
        $orderStatus = $request->input('order_status');
        $order_id = $request->input('order_id');

        $order = Order::find($order_id);

        if ($order->user_id)

        $order->order_status = $orderStatus;
        $order->save();

        return $this->successResponse(['order' => $order]);
    }
}
