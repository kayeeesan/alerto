<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SensorUnderAlerto extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'municipality' => $this->municipality,
            'municipality_id' => $this->municipality_id,
            'river' => $this->river,
            'river_id' => $this->river_id,
            'long' => $this->long,
            'lat' => $this->lat,
            'status' => $this->status,
            'sensor_type' => $this->sensor_type
        ];
    }
}
