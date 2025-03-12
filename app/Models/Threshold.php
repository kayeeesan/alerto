<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Threshold extends Model
{
    use HasFactory, softDeletes;

    protected $fillable = [
        'river',
        'sensor',
        'municipality',
        'xs_date'
    ];

     /**
     * Get the river associated with the threshold.
     */
    public function river()
    {
        return $this->belongsTo(River::class);
    }

    /**
     * Get the sensor associated with the threshold.
     */
    public function sensor()
    {
        return $this->belongsTo(SensorUnderAlerto::class); //Model name
    }

    /**
     * Get the municipality associated with the threshold.
     */
    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    /**
     * Accessor to fetch sensor details dynamically.
     */
    public function getSensorDetailsAttribute()
    {
        return $this->sensor ? [
            'id' => $this->sensor->id,
            'name' => $this->sensor->name,
            'baseline' => $this->sensor->baseline,
            'sixty_percent' => $this->sensor->sixty_percent,
            'eighty_percent' => $this->sensor->eighty_percent,
            'one_hundred_percent' => $this->sensor->one_hundred_percent,
        ] : null;
    }
}
