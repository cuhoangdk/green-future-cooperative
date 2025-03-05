<?php
namespace App\Http\Requests\ProductUnit;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductUnitRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:50|unique:product_units,name',
            'description' => 'nullable|string',
            'allow_decimal' => 'required|boolean',
        ];
    }
}