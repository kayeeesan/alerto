<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\UsesUuid;

class SensorsHistory extends Model
{
    use HasFactory, UsesUuid;

    protected $fillable = [
        'uuid',
        'sensor_uuid',
        'device_water_level',
        'device_rain_amount',
        'recorded_at',
        'sync_at',
        'created_at',
        'updated_at'
    ];

    public function sensor()
    {
        return $this->belongsTo(SensorUnderAlerto::class, 'sensor_uuid', 'uuid');
    }
}
