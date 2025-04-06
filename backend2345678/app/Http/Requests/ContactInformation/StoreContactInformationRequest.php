<?php

namespace App\Http\Requests\ContactInformation;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactInformationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'type' => 'required|in:phone,email,address',
            'label' => 'required|string|max:255',
            'value' => 'required|string|max:255',
            'sort_order' => 'sometimes|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ];
    }
}