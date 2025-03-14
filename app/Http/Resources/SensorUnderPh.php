<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SensorUnderPh extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'river' => $this->river ? $this->river->name : null,
            'municipality' => $this->municipality ? $this->municipality->name : null,
            'long' => $this->long,
            'lat' => $this->lat,
            'status' => $this->status
        ];
    }
}
