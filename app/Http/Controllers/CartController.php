<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Get all items that are in user's cart
     */
    public function userCartItems()
    {
        $user = auth()->user();
        $cartItems = $user->cart?->items ?? collect();  // if no items just return empty collection

        return $this->successResponse($cartItems);
    }

    /**
     * Add product to the cart
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:App\Models\Product,id'],
            'quantity' => ['required', 'numeric', 'min:1'],
        ]);

        $user = auth()->user();
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
                'cart_id' => $cartId
            ]);
        }

        return $this->successResponse($cartItem);
    }

    /**
     * Display the specified resource.
     */
    public function show(CartItem $cartItem)
    {
        return $this->successResponse($cartItem);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CartItem $cartItem)
    {
        //
    }

    /**
     * Remove cart item
     */
    public function destroy(CartItem $cartItem)
    {
        $cartItem->delete();
        return $this->noContent();
    }
}
