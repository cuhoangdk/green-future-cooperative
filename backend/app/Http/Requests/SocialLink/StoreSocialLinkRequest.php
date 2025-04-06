<?php

namespace App\Http\Requests\SocialLink;

use Illuminate\Foundation\Http\FormRequest;

class StoreSocialLinkRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'platform' => 'required|in:facebook,tiktok,youtube,instagram',
            'url' => 'required|url|max:255',
            'is_active' => 'sometimes|boolean'
        ];
    }
}
