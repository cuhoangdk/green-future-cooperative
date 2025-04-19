<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\ProductQuantityPrice;

class CartItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $calculatedPrice = $this->getPriceForQuantity($this->product_id, $this->quantity);
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'purchase_price' => $calculatedPrice, 
            'product' => new ProductResource($this->whenLoaded('product')),
            'invalid_quantity' => $this->invalid_quantity ?? false, // Thêm trường kiểm tra
            'invalid_message' => $this->invalid_message ?? null, // Thông báo nếu không hợp lệ            
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
    protected function getPriceForQuantity(string $productId, float $quantity)
    {
        $priceRecordExists = ProductQuantityPrice::where('product_id', $productId)->exists();

        if (!$priceRecordExists) {
            return 'Contact Price';
        }

        $minQuantity = ProductQuantityPrice::where('product_id', $productId)->min('quantity');
        if ($minQuantity !== null && $quantity < $minQuantity) {
            return null; // Hoặc xử lý khác nếu cần
        }

        $priceRecord = ProductQuantityPrice::where('product_id', $productId)
            ->where('quantity', '<=', $quantity)
            ->orderBy('quantity', 'desc')
            ->first();

        return $priceRecord ? $priceRecord->price : 'Contact Price';
    }
}
