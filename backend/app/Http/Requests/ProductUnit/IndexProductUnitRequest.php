<?php

namespace App\Http\Requests\ProductUnit;

use Illuminate\Foundation\Http\FormRequest;

class IndexProductUnitRequest extends FormRequest
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
            'sort_by'        => 'nullable|string|in:name,created_at,updated_at', 
            'sort_direction' => 'nullable|string|in:asc,desc', 
        ];
    }
}
