<?php

namespace App\Http\Requests\PostComments;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
        // $comment = $this->route('comment'); // Lấy comment từ route
        // return Auth::guard('api_customers')->id() === $comment->customer_id || Auth::guard('api_users')->check();
    }

    public function rules(): array
    {
        return [
            'content' => 'required|string|max:1000',
        ];
    }
}
