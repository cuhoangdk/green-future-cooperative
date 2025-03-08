<?php

namespace App\Http\Requests\SliderImage;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSliderImageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'sometimes|string|max:255',
            'image_url' => 'sometimes|url|max:255',
            'link_url' => 'sometimes|nullable|url|max:255',
            'sort_order' => 'sometimes|integer|min:0',
            'is_active' => 'sometimes|boolean',
            'start_date' => 'sometimes|nullable|date',
            'end_date' => 'sometimes|nullable|date|after_or_equal:start_date',
        ];
    }
}