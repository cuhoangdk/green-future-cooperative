<?php

namespace App\Http\Requests\ProductUnit;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductUnitRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {        

        return [
            'name' => 'sometimes|string|max:50|unique:product_units,name,' . $this->id,
            'description' => 'nullable|string',
            'allow_decimal' => 'required|boolean',
        ];
    }
}