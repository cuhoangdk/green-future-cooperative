<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */    
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'category_name' => $this->category ? $this->category->name : null,  // Lấy name từ bảng post_category
            'author_name' => $this->author ? $this->author->full_namename : null,  // Lấy name từ bảng users
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
