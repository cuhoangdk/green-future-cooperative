<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Điều chỉnh theo logic phân quyền nếu cần
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
            'farm_id' => 'required|exists:farms,id',
            'category_id' => 'required|exists:product_categories,id',
            'unit_id' => 'required|exists:product_units,id',
            'description' => 'nullable|string',
            'seed_supplier' => 'nullable|string|max:255',
            'cultivated_area' => 'nullable|numeric|min:0',
            'sown_at' => 'nullable|date',
            'harvested_at' => 'nullable|date|after_or_equal:sown_at',
            'pricing_type' => 'required|in:fix,flexible,contact',
            'stock_quantity' => 'required|numeric|min:0',
            'status' => 'in:growing,selling,stopped',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keyword' => 'nullable|string',
            'expired' => 'nullable|integer|min:1',
        ];
    }
}