<?php

namespace App\Http\Requests\ProductImage;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductImageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'image_urls' => 'sometimes|array',
            'image_urls.*' => 'image|mimes:jpeg,png,jpg,gif|max:4096',
            'sort_order' => 'sometimes|integer|min:0',
            'is_primary' => 'sometimes|boolean',            
            'title' => 'nullable|string|max:255',
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
        });
    }
}