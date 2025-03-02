<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class YouTubeUrl implements Rule
{
    public function passes($attribute, $value)
    {
        if (empty($value)) {
            return true; // Cho phép nullable
        }

        $pattern = '/^https?:\/\/(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)[a-zA-Z0-9_-]+$/';
        return preg_match($pattern, $value) === 1;
    }

    public function message()
    {
        return 'The :attribute must be a valid YouTube URL (e.g., https://youtube.com/watch?v=abc123 or https://youtu.be/abc123).';
    }
}