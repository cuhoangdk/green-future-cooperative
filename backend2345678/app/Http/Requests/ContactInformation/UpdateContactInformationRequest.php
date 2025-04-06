<?php

namespace App\Http\Requests\ContactInformation;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContactInformationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'type' => 'sometimes|in:phone,email,address',
            'label' => 'sometimes|string|max:255',
            'value' => 'sometimes|string|max:255',
            'sort_order' => 'sometimes|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ];
    }
}