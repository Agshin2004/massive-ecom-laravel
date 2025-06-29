<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_items' => $this->whenLoaded('orderItems'),
            'products_sum' => $this->calculateTotal(),
        ];
    }

    public function calculateTotal()
    {
        // $orderItems = $this->whenLoaded('orderItems');
        $sum = collect($this->whenLoaded('orderItems'))
            ->reduce(
                fn ($acc, $item) => $acc + $item->product->price * $item->quantity,
                0
            );

        return $sum;
    }
}
