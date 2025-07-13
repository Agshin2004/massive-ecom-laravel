<?php

namespace App\DTOs;

class CategoryDTO
{
    public function __construct(
        public string $name
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
