<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index(Request $request)
    {
        return $this->errorResponse('Url Not Found', 404);
    }

    /**
     * Add product to the cart.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:App\Models\Product,id'],
            'quantity' => ['required', 'numeric', 'min:1'],
        ]);

        $user = Auth::user();
        $cartId = $user->cart->id;  // since it is 1:1 relationship accessing cart as property

        $cartItem = CartItem::where('cart_id', $cartId)
            ->where('product_id', $request->input('product_id'))
            ->first();

        if ($cartItem) {
            // if $cartItem is present meaning produc tis already in cart and just quantity needs to be updated
            $cartItem->quantity += (int) $request->input('quantity');
            $cartItem->save();
        } else {
            // if no $cartItem meaning the product is not added and gotta create it
            $cartItem = CartItem::create([
                'product_id' => $request->input('product_id'),
                'quantity' => $request->input('quantity'),
                'cart_id' => $cartId,
            ]);
        }

        return $this->successResponse($cartItem);
    }

    /**
     * Display the specified resource.
     */
    public function show(CartItem $cartItem)
    {
        return $this->successResponse([
            'cart_items' => $cartItem,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CartItem $cartItem) {}

    /**
     * Remove cart item.
     */
    public function destroy(CartItem $cartItem)
    {
        $cartItem->delete();

        return $this->noContent();
    }
}
