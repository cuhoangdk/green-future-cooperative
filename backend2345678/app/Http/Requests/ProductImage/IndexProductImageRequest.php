<?php

namespace App\Http\Requests\ProductImage;

use Illuminate\Foundation\Http\FormRequest;

class IndexProductImageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Các quy tắc validate
     */
    public function rules()
    {
        return [
            'product_id' => 'required|exists:products,id', // Lấy từ route
            'sort_by' => 'sometimes|in:sort_order,created_at,is_primary', // Các cột hợp lệ để sắp xếp
            'sort_direction' => 'sometimes|in:asc,desc',
            'per_page' => 'sometimes|integer|min:1|max:100', // Giới hạn phân trang từ 1-100
        ];
    }

    /**
     * Gán product_id từ route parameter
     */
    public function validationData()
    {
        return array_merge($this->all(), [
            'product_id' => $this->route('product_id'),
        ]);
    }
}
