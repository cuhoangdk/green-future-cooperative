<?php

namespace App\Http\Requests\PostComments;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('api_customers')->check();
    }

    public function rules(): array
    {
        return [
            'content' => 'required|string|max:1000', 
        ];
    }
}
