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
            'baseline' => $this->baseline,
            'sixty_percent' => $this->sixty_percent,
            'eighty_percent' => $this->eighty_percent,
            'one_hundred_percent' => $this->one_hundred_percent,
        ];
    }
}
