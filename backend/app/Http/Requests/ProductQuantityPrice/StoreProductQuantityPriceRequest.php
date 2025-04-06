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
            'prices' => 'required|array|min:1', // Mảng prices, ít nhất 1 phần tử
            'prices.*.quantity' => 'required|numeric|min:0', // Quantity cho mỗi phần tử
            'prices.*.price' => 'required|numeric|min:0', // Price cho mỗi phần tử
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