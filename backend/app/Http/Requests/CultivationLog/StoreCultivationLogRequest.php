<?php

namespace App\Http\Requests\CultivationLog;

use App\Rules\YouTubeUrl;
use Illuminate\Foundation\Http\FormRequest;

class StoreCultivationLogRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [            
            'activity' => 'required|string|max:255',
            'fertilizer_used' => 'nullable|string|max:255',
            'pesticide_used' => 'nullable|string|max:255',
            'image_url' => 'required_without:video_url|file|image|max:10240',
            'video_url' => ['required_without:image_url', 'string', 'max:255', new YouTubeUrl],
            'notes' => 'nullable|string',
        ];
    }
    /**
     * Kiểm tra product_id từ body (nếu có) phải khớp với route
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $routeProductId = (int) $this->route('product_id');
            $bodyProductId = $this->input('product_id');

            if ($bodyProductId !== null && $bodyProductId != $routeProductId) {
                $validator->errors()->add(
                    'product_id',
                    'The product_id in the request body must match the product_id in the URL or be omitted.'
                );
            }
        });
    }
}