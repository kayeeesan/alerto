<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\SensorUnderPh;
use App\Http\Resources\SensorUnderAlerto;

class Threshold extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

     
     public function toArray(Request $request)
    {
        $sensor = $this->sensorable;

        return [
            'id' => $this->id,
            'sensor' => $sensor ? [
                'id' => $sensor->id,
                'name' => $sensor->name ?? '',
                'type' => $this->sensorable_type,
                'river' => $sensor->river ?? null,
                'municipality' => $sensor->municipality ?? null,
            ] : null,
            'baseline' => $this->baseline,
            'sixty_percent' => $this->sixty_percent,
            'eighty_percent' => $this->eighty_percent,
            'one_hundred_percent' => $this->one_hundred_percent,
            'xs_date' => $this->xs_date
        ];
    }

     
}
