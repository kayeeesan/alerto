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
            'municipality' => [
                'id' => $this->municipality?->id,
                'name' => $this->municipality?->name,
                'province' => [
                    'id' => $this->province?->id,
                    'name' => $this->province?->name,
                ],
            ],
            'municipality_id' => $this->municipality_id,
            'river' => [
                'id' => $this->river?->id,
                'name' => $this->river?->name,
            ],
            'river_id' => $this->river_id,
            'long' => $this->long,
            'lat' => $this->lat,
            'status' => $this->status,
            'sensor_type' => $this->sensor_type,
            'rain_amount' => $this->threshold?->rain_amount,
            'water_level' => $this->threshold?->water_level
        ];
    }
}
