<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class SearchUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Điều chỉnh nếu cần
    }

    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
            'per_page' => 'nullable|integer|min:1|max:100',
            'sort_by' => 'nullable|string|in:created_at,updated_at,email,full_name',
            'sort_direction' => 'nullable|string|in:asc,desc',
        ];
    }
}
