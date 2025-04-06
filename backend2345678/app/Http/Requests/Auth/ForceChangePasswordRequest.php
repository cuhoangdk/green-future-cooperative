<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ForceChangePasswordRequest extends FormRequest
{
    public function rules()
    {
        return [            
            'password' => 'required|min:6|confirmed',
        ];
    }
}
