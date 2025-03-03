<?php

namespace App\Http\Requests\ProductQuantityPrice;

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
            $routeProductId = (int) $this->route('product_id');
            $bodyProductId = $this->input('product_id');

            if ($bodyProductId !== null && $bodyProductId != $routeProductId) {
                $validator->errors()->add(
                    'product_id',
                    'The product_id in the request body must match the product_id in the URL or be omitted.'
                );
            }
        });
    }
}