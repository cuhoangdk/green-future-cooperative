<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadFileService
{
    /**
     * Upload ảnh lên storage và trả về đường dẫn.
     *
     * @param UploadedFile $file
     * @param string $folder
     * @param string|null $disk (default: 'public')
     * @return string|null
     */
    public function uploadImage(UploadedFile $file, string $folder, string $disk = 'public'): ?string
    {
        // Tạo tên file duy nhất
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

        // Lưu file vào storage
        $path = $file->storeAs("public/{$folder}", $filename, $disk);

        // Trả về đường dẫn tương đối, thay 'public/' bằng '/storage/'
        return $path ? str_replace('public/', '/storage/', $path) : null;
    }


    /**
     * Xóa ảnh cũ trong storage.
     *
     * @param string|null $filePath
     * @param string $disk
     * @return bool
     */
    public function deleteImage(?string $filePath, string $disk = 'public'): bool
    {
        if (!$filePath) {
            return false;
        }

        // Chuyển đổi đường dẫn từ '/storage/posts/...jpg' thành 'public/posts/...jpg' để xóa
        $storagePath = str_replace('/storage/', 'public/', $filePath);

        if (Storage::disk($disk)->exists($storagePath)) {
            return Storage::disk($disk)->delete($storagePath);
        }

        return false;
    }

}
