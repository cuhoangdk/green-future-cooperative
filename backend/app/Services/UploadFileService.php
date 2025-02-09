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
        $path = $file->storeAs($folder, $filename, $disk);

        return $path ? Storage::disk($disk)->url($path) : null;
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
        if ($filePath && Storage::disk($disk)->exists($filePath)) {
            return Storage::disk($disk)->delete($filePath);
        }
        return false;
    }
}
