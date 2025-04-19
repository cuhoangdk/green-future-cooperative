<?php

namespace App\Http\Requests\ProductImage;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductImageRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Điều chỉnh theo logic phân quyền nếu cần
    }

    public function rules()
    {
        return [
            'images' => 'required|array|min:1', // Tên mới: 'images' thay vì 'image_urls'
            'images.*.image_url' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096', // File ảnh
            'images.*.sort_order' => 'sometimes|integer|min:0',
            'images.*.is_primary' => 'sometimes|boolean',
            'images.*.title' => 'nullable|string|max:255',
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