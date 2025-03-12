<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Threshold extends JsonResource
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
            'river' => $this->river ? $this->river->name : null,
            'sensor' => $this->sensor ? $this->sensor->name : null,
            'municipality' => $this->municipality ? $this->municipality->name : null,
            'xs_date' => $this->xs_date, 
        ];
    }
}
