<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class ActivityLogResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_type' => $this->user_type,
            'user_id' => $this->user_id,
            'action' => $this->action,
            'entity_type' => $this->entity_type,
            'old_data' => $this->formatData($this->old_data),
            'new_data' => $this->formatData($this->new_data),
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }

    protected function formatData($data)
    {
        if (!$data) {
            return null;
        }

        $decoded = json_decode($data, true);
        if (isset($decoded['created_at'])) {
            $decoded['created_at'] = Carbon::parse($decoded['created_at'])->timezone(config('app.timezone'))->toDateTimeString();
        }
        if (isset($decoded['updated_at'])) {
            $decoded['updated_at'] = Carbon::parse($decoded['updated_at'])->timezone(config('app.timezone'))->toDateTimeString();
        }

        return $decoded;
    }
}