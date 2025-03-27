<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
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
            'customer_id' => $this->customer_id,
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'calculated_price' => $this->calculated_price, // Thêm giá đã tính
            'product' => new ProductResource($this->whenLoaded('product')),
            'invalid_quantity' => $this->invalid_quantity ?? false, // Thêm trường kiểm tra
            'invalid_message' => $this->invalid_message ?? null, // Thông báo nếu không hợp lệ            
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
