<?php

namespace App\Http\Requests\Posts;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePostRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string|max:500',
            'content' => 'required',
            'featured_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'category_id' => 'required|exists:post_categories,id',
            'post_status' => 'in:draft,published,archived',
            'published_at' => 'nullable|date',
            'is_hot' => 'boolean',
            'is_featured' => 'boolean',
            'tags' => 'nullable|string|max:255', 
            'meta_title' => 'nullable|string|max:255', 
            'meta_description' => 'nullable|string|max:500',
            'hot_order' => 'nullable|integer|min:0',
            'featured_order' => 'nullable|integer|min:0',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
