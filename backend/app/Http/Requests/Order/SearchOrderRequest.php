<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchOrderRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:2000|max:2100',
            'month' => 'nullable|integer|min:1|max:12',
            'day' => 'nullable|integer|min:1|max:31',
            'status' => 'nullable|in:pending,processing,delivered,cancelled',
            'per_page' => 'nullable|integer|min:1|max:100',
            'sort_by' => 'nullable|in:id,order_code,status,total_price,final_total_amount,created_at,updated_at',
            'sort_direction' => 'nullable|in:asc,desc',
        ];
    }
}