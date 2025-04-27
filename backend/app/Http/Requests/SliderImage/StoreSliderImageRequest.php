<?php

namespace App\Http\Requests\SliderImage;

use Illuminate\Foundation\Http\FormRequest;

class StoreSliderImageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'nullable|string|max:255',
            'image_url' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'link_url' => 'nullable|url|max:255',
            'sort_order' => 'sometimes|integer|min:0',
            'is_active' => 'sometimes|boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];
    }
}