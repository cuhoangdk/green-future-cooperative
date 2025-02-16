<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */    
    public function toArray(Request $request): array
    {
        // Lấy dữ liệu mặc định từ parent::toArray()
        $data = parent::toArray($request);

        // Bỏ các trường không mong muốn
        $data = Arr::except($data, ['category_id', 'user_id']);
        
        return $data;
    }
}
