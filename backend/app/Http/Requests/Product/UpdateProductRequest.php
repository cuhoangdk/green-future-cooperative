<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Điều chỉnh theo logic phân quyền nếu cần
    }

    public function rules()
    {
        return [
            'name' => 'sometimes|string|max:255',
            'user_id' => 'sometimes|exists:users,id',
            'farm_id' => 'sometimes|exists:farms,id',
            'category_id' => 'sometimes|exists:product_categories,id',
            'unit_id' => 'sometimes|exists:product_units,id',
            'description' => 'nullable|string',
            'seed_supplier' => 'nullable|string|max:255',
            'cultivated_area' => 'nullable|numeric|min:0',
            'sown_at' => 'nullable|date',
            'harvested_at' => 'nullable|date|after_or_equal:sown_at',
            'pricing_type' => 'sometimes|in:fix,flexible,contact',
            'base_price' => 'sometimes|numeric|min:0',
            'stock_quantity' => 'sometimes|numeric|min:0',
            'is_active' => 'sometimes|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keyword' => 'nullable|string',
        ];
    }
}