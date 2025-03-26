<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Staff extends JsonResource
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
            // 'user_id' => $this->user_id,
            'username' => $this->username,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'mobile_number' => $this->mobile_number,
            'role' => $this->role,
            'region' => $this->region,
            'province' => $this->province,
            'municipality' => $this->municipality,
            'river' => $this->river,
        ];
    }
}
