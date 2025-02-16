<?php

namespace App\Http\Requests\Posts;

use Illuminate\Foundation\Http\FormRequest;

class SearchPostRequest extends FormRequest
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
            'sort_by'        => 'nullable|string|in:title,created_at,updated_at,published_at,post_status', 
            'sort_direction' => 'nullable|string|in:asc,desc', 
            'search'         => 'nullable|string|max:255', 
            'user_id'        => 'nullable|integer|exists:users,id', 
            'category_id'    => 'nullable|integer|exists:post_categories,id',
            'status'         => 'nullable|string|in:draft,published,archived', 
            'start_date'     => 'nullable|date', 
            'end_date'       => 'nullable|date', 
            'is_hot'         => 'nullable|boolean', 
            'is_featured'    => 'nullable|boolean', 
        ];
    }
}
