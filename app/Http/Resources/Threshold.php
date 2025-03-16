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
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'sensor' => $this->sensor,
            'baseline' => $this->baseline, 
            'sixty_percent' => $this->sixty_percent, 
            'eighty_percent' => $this->eighty_percent, 
            'one_hundred_percent' => $this->one_hundred_percent, 
            'xs_date' => $this->xs_date, 
        ];
    }
}
