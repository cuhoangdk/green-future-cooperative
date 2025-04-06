<?php

namespace App\Http\Resources;

use Arr;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CultivationLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        $data = Arr::except($data, ['user.remember_token']);
        return $data;
    }
}
