<?php

namespace App\DTOs;

class OrderItemDTO
{
    public function __construct(
        public string $orderId,
        public int $productId,
        public int $quantity
    ) {}

    public function toArray(): array
    {
        return [
            'orderId' => $this->orderId,
            'productid' => $this->productId,
            'quantity' => $this->quantity,
        ];
    }
}
