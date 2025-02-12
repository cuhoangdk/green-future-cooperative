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
        $data = Arr::except($data, ['category_id', 'author_id', 'category', 'author']);

        // Thêm các trường bổ sung
        $data['category_name'] = $this->category ? $this->category->name : null;
        $data['author_name'] = $this->author ? $this->author->full_name : null;

        return $data;
    }
}
