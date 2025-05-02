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
            'user_id' => $this->user_id,
            'username' => $this->user?->username,
            'full_name' => $this->user?->full_name,
            'first_name' => $this->user?->first_name,
            'last_name' => $this->user?->last_name,
            'middle_name' => $this->user?->middle_name,
            'mobile_number' => $this->mobile_number,
            'role' => $this->role,
            'region_id' => $this->region_id,
            'region' => $this->region,
            'province_id' => $this->province_id,
            'province' => $this->province,
            'municipality_id' => $this->municipality_id,
            'municipality' => $this->municipality,
            'river' => $this->river,
            'fb_lgu' => $this->fb_lgu,
            'status' => $this->user?->status
        ];
    }

}
