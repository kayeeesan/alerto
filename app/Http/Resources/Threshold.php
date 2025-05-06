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
        // $sensorData = null;

        // // Checking the type of sensorable and assigning the correct resource
        // if ($this->sensorable instanceof \App\Models\SensorUnderPh) {
        //     $sensorData = new SensorUnderPh($this->sensorable);
        // } elseif ($this->sensorable instanceof \App\Models\SensorUnderAlerto) {
        //     $sensorData = new SensorUnderAlerto($this->sensorable);
        // }
     
         return [
             'id' => $this->id,
             'sensor' => [
                    'id' => $this->sensorable->id,
                    'name' => $this->sensorable->name ?? '',
                    'type' => $this->sensorable_type, // This is crucial
                    'river' => $this->sensorable->river ?? null,
                    'municipality' => $this->sensorable->municipality ?? null,
                ], 
             'baseline' => $this->baseline,
             'sixty_percent' => $this->sixty_percent,
             'eighty_percent' => $this->eighty_percent,
             'one_hundred_percent' => $this->one_hundred_percent,
             'xs_date' => $this->xs_date,
             'water_level' => $this->water_level,
             'rain_amount' => $this->rain_amount
         ];
     }
     
}
