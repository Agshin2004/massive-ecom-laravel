<?php

namespace App\DTOs;

class ProductDTO
{
    public function __construct(
        public string $name,
        public string $description,
        public int $categoryId,
        public float $price,
        public string $sellerId
    ) {}
}
