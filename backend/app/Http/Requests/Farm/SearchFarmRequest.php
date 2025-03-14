<?php

namespace App\Http\Requests\Farm;

use Illuminate\Foundation\Http\FormRequest;

class SearchFarmRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'per_page'       => 'nullable|integer|min:1|max:100', 
            'sort_by'        => 'nullable|string|in:name, province, district, ward, created_at, updated_at', 
            'sort_direction' => 'nullable|string|in:asc,desc', 
            'search'         => 'nullable|string|max:255',
            'user_id'        => 'nullable|integer|exists:users,id',
        ];
    }
}
