<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);

        $user = auth('api_users')->user();
        $isSuperAdmin = $user && $user->is_super_admin;

        // Thêm trường items với message kiểu bool, không kèm theo product
        $data['items'] = collect($data['items'])->map(function ($item) use ($user, $isSuperAdmin) {
            $belongsToCurrentUser = $user && isset($item['product']['user_id']) && $item['product']['user_id'] === $user->id;
            $item['flag'] = !$isSuperAdmin && !$belongsToCurrentUser; // true nếu không thuộc về user hiện tại và không phải super_admin
            unset($item['product']); // Loại bỏ trường product
            return $item;
        })->all();

        return $data;
    }
}