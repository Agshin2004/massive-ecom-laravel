<?php

use App\Models\Product;

function sellerHasProduct($productName, $productPrice)
{
    $exists = Product::where('name', $productName)->where('price', $productPrice)->get();
    if ($exists->isNotEmpty()) {
        abort(400, 'Product with same name and price already exists.');
    }
}
