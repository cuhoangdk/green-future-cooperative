<?php

namespace App\Http\Requests\ProductQuantityPrice;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductQuantityPriceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'quantity' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $routeProductId = (int) $this->route('product_id');
            $bodyProductId = $this->input('product_id');

            if ($bodyProductId !== null && $bodyProductId != $routeProductId) {
                $validator->errors()->add(
                    'product_id',
                    'The product_id in the request body must match the product_id in the URL or be omitted.'
                );
            }
            $product = Product::with('unit')->findOrFail($routeProductId);
            $allowDecimal = $product->unit->allow_decimal ?? true; // Mặc định true nếu không có unit

            $quantity = $this->input('quantity');
            if (!$allowDecimal && floor($quantity) != $quantity) {
                $validator->errors()->add(
                    'quantity',
                    'The quantity must be an integer for this product unit (e.g., 1, 2, etc.).'
                );
            }
        });
    }
}