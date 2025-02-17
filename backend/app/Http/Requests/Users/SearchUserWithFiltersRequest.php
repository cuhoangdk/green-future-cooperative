<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class SearchUserWithFiltersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Điều chỉnh nếu cần
    }

    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'is_super_admin' => 'nullable|boolean',
            'is_banned' => 'nullable|boolean',
            'province' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'ward' => 'nullable|string|max:255',
            'per_page' => 'nullable|integer|min:1|max:100',
            'sort_by' => 'nullable|string|in:created_at,updated_at,email,full_name',
            'sort_direction' => 'nullable|string|in:asc,desc',
        ];
    }
}
