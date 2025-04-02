<?php

namespace App\Http\Requests\ActivityLog;

use Illuminate\Foundation\Http\FormRequest;

class SearchActivityLogRequest extends FormRequest
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
            'sort_by'        => 'nullable|string|in:name,user_type,action,entity_type,created_at', 
            'sort_direction' => 'nullable|string|in:asc,desc', 
            'search'         => 'nullable|string|max:255', 
            'user_type'      => 'nullable|string|in:system,member,customer', 
            'start_date'     => 'nullable|date', 
            'end_date'       => 'nullable|date', 
        ];
    }
}
