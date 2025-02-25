<?php

namespace App\Http\Resources;

use Http;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
class CustomerAddressResource extends JsonResource
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
        $data = Arr::except($data, ['deleted_at']);
        
        return $data;
    }
}
