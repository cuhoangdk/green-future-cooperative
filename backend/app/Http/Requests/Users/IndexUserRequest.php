<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class IndexUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'per_page'       => 'nullable|integer|min:1|max:100', 
            'sort_by'        => 'nullable|string|in:full_name,email,created_at,updated_at,province,district,ward', 
            'sort_direction' => 'nullable|string|in:asc,desc', 
        ];
    }
}
