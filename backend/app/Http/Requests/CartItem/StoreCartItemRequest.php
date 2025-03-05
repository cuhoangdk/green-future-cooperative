<?php

namespace App\Http\Requests\CartItem;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Product;

class StoreCartItemRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.1',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $productId = $this->input('product_id');
            $quantity = $this->input('quantity');
            $product = Product::with('unit')->find($productId);

            if (!$product) {
                $validator->errors()->add('product_id', 'The selected product does not exist.');
                return;
            }

            // Kiểm tra allow_decimal từ ProductUnit
            if ($product->unit && !$product->unit->allow_decimal && floor($quantity) != $quantity) {
                $validator->errors()->add(
                    'quantity',
                    'The quantity must be an integer for this product (e.g., 1, 2, etc.).'
                );
            }

            // Kiểm tra stock_quantity
            if ($quantity > $product->stock_quantity) {
                $validator->errors()->add(
                    'quantity',
                    "The quantity must not exceed the available stock ({$product->stock_quantity})."
                );
            }
        });
    }
}