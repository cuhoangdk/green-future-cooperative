<?php

namespace App\Http\Requests\CartItem;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\CartItem;

class UpdateCartItemRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'quantity' => 'sometimes|numeric|min:0.1',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->has('quantity')) {
                $cartItem = CartItem::findOrFail($this->route('id'));
                $product = $cartItem->product()->with('unit')->first();

                $quantity = $this->input('quantity');
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
            }
        });
    }
}