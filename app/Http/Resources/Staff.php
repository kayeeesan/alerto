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
            'middle_name' => $this->middle_name,
            'mobile_number' => $this->mobile_number,
            'role' => new RoleResource($this->whenLoaded('role')),
            'government_agency' => $this->government_agency,
            'region' => new RegionResource($this->whenLoaded('region')),
            'province' => new ProvinceResource($this->whenLoaded('province')),
            'municipality' => new MunicipalityResource($this->whenLoaded('municipality')),
            'river' => new RiverResource($this->whenLoaded('river')),
            'lgu_fb' => $this->lgu_fb,
            'status' => $this->status,
            'created_at' => $this->created_at,
        ];
    }
}
