<?php

namespace App\Http\Requests\ProductQuantityPrice;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductQuantityPriceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'quantity' => 'sometimes|numeric|min:0',
            'price' => 'sometimes|numeric|min:0',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $routeProductId = (string) $this->route('product_id');
            $bodyProductId = $this->input('product_id');

            if ($bodyProductId !== null && $bodyProductId != $routeProductId) {
                $validator->errors()->add(
                    'product_id',
                    'The product_id in the request body must match the product_id in the URL or be omitted.'
                );
            }
            if ($this->has('quantity')) {
                $product = Product::with('unit')->findOrFail($routeProductId);
                $allowDecimal = $product->unit->allow_decimal ?? true;

                $quantity = $this->input('quantity');
                if (!$allowDecimal && floor($quantity) != $quantity) {
                    $validator->errors()->add(
                        'quantity',
                        'The quantity must be an integer for this product unit (e.g., 1, 2, etc.).'
                    );
                }
            }
        });
    }
}