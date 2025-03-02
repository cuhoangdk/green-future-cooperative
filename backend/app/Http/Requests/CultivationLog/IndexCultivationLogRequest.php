<?php

namespace App\Http\Requests\CultivationLog;

use Illuminate\Foundation\Http\FormRequest;

class IndexCultivationLogRequest extends FormRequest
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
            'product_id' => 'required|exists:products,id',
            'sort_by' => 'sometimes|in:created_at,activity', // Các cột hợp lệ để sắp xếp
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
            'product_id' => $this->route('product_id'), // Lấy từ route
        ]);
    }
    /**
     * Thông báo lỗi tùy chỉnh
     */
    public function messages()
    {
        return [
            'sort_by.in' => 'The sort_by field must be one of: created_at, activity.',
            'sort_direction.in' => 'The sort_direction field must be either asc or desc.',
            'per_page.integer' => 'The per_page field must be an integer.',
            'per_page.min' => 'The per_page field must be at least 1.',
            'per_page.max' => 'The per_page field must not exceed 100.',
        ];
    }
}