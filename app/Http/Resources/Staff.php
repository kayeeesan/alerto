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
            'username' => $this->username,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
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
            'status' => $this->status
        ];
    }
}
