<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Threshold extends Model
{
    use HasFactory;

    protected $fillable = [
        'sensor_id',
        'baseline',
        'sixty_percent',
        'eighty_percent',
        'one_hundred_percent',
        'xs_date'
    ];

    public function sensor()
    {
        return $this->belongsTo(SensorUnderAlerto::class, 'sensor_id'); //Model name
    }
}
