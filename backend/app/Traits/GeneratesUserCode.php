<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait GeneratesUserCode
{
    /**
     * Tạo `usercode` duy nhất cho model dựa trên cột full_name.
     *
     * @param string $fullName
     * @param string $modelClass
     * @return string
     */
    public static function generateUserCode(string $fullName, string $modelClass): string
    {
        // Tạo prefix từ full_name: loại bỏ dấu, chuyển thành chữ in hoa, viết liền
        $namePrefix = Str::upper(Str::slug($fullName, '_'));

        // Lấy số thứ tự lớn nhất hiện có trong bảng của model tương ứng
        $latestModel = $modelClass::withTrashed()->where('usercode', 'like', "{$namePrefix}_%")
            ->orderBy('id', 'desc')
            ->first();

        // Xác định số tiếp theo
        $nextNumber = 1; // Mặc định là 1 nếu không tìm thấy bản ghi nào trùng prefix
        if ($latestModel) {
            $lastNumber = (int) Str::afterLast($latestModel->usercode, '_');
            $nextNumber = $lastNumber + 1;
        }

        // Format số thứ tự với 4 chữ số (0001, 0002, ...)
        $formattedNumber = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // Kết hợp prefix và số thứ tự, có dấu gạch dưới
        return "{$namePrefix}_{$formattedNumber}";
    }

    /**
     * Boot trait để tự động tạo `usercode` khi tạo model.
     *
     * @return void
     */
    public static function bootGeneratesUserCode()
    {
        static::creating(function ($model) {
            if (empty($model->usercode)) {
                // Tạo usercode cho model hiện tại
                $model->usercode = static::generateUserCode($model->full_name, static::class);
            }
        });
    }
}
