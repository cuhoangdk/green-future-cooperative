<?php
namespace App\Http\Requests\PostComments;

use Illuminate\Foundation\Http\FormRequest;

class SearchCommentRequest extends FormRequest
{
    /**
     * Xác định xem user có được phép gửi request này không.
     */
    public function authorize(): bool
    {
        return true; // Cho phép tất cả user có thể tìm kiếm
    }

    /**
     * Định nghĩa rules validation.
     */
    public function rules(): array
    {
        return [
            'search'   => ['nullable', 'string', 'max:255'], // Tìm kiếm theo nội dung (tối đa 255 ký tự)
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'], // Số lượng comment mỗi trang
        ];
    }
}
