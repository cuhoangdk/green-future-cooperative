<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class SearchProductRequest extends FormRequest
{
    /**
     * Xác định user có quyền gửi request này không
     */
    public function authorize()
    {
        return true; // Điều chỉnh theo logic phân quyền nếu cần
    }

    /**
     * Các quy tắc validate
     */
    public function rules()
    {
        return [
            'search' => 'sometimes|string|max:255', // $query từ route
            'sort_by' => 'sometimes|in:created_at,name,base_price,stock_quantity,views,sold_quantity',
            'sort_direction' => 'sometimes|in:asc,desc',
            'per_page' => 'sometimes|integer|min:1|max:100',
        ];
    }
}