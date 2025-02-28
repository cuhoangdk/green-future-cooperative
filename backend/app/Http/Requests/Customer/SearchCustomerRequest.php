<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class SearchCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'per_page'       => 'nullable|integer|min:1|max:100', 
            'sort_by'        => 'nullable|string|in:full_name, email, phone_number,created_at,updated_at', 
            'sort_direction' => 'nullable|string|in:asc,desc', 
            'search'         => 'nullable|string|max:255',];
    }
}
