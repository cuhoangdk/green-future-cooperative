<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class FilterProductRequest extends FormRequest
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
            'search' => 'sometimes|string|max:255',
            'category_id' => 'sometimes|exists:product_categories,id',
            'unit_id' => 'sometimes|exists:product_units,id',
            'user_id' => 'sometimes|exists:users,id',
            'farm_id' => 'sometimes|exists:farms,id',
            'pricing_type' => 'sometimes|in:fix,flexible,contact',
            'status' => 'in:growing,selling,stopped',
            'base_price_min' => 'sometimes|numeric|min:0',
            'base_price_max' => 'sometimes|numeric|min:0',
            'stock_quantity_min' => 'sometimes|numeric|min:0',
            'stock_quantity_max' => 'sometimes|numeric|min:0',
            'sown_at_from' => 'sometimes|date',
            'sown_at_to' => 'sometimes|date|after_or_equal:sown_at_from',
            'harvested_at_from' => 'sometimes|date',
            'harvested_at_to' => 'sometimes|date|after_or_equal:harvested_at_from',
            'sort_by' => 'sometimes|in:created_at,updated_at,name,stock_quantity,views,sold_quantity',
            'sort_direction' => 'sometimes|in:asc,desc',
            'per_page' => 'sometimes|integer|min:1|max:100',
        ];
    }
}