<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;

class SensorUnderPh extends Model
{
    use HasFactory; 
    use SoftDeletes; 
    use UsesUuid;

    protected $table = 'sensors_under_ph'; 

    protected $fillable = [
        'uuid',
        'name',
        'device_id',
        'device_rain_amount',
        'device_water_level',
        'previous_water_level',
        'previous_rain_amount',  
        'river_id',
        'municipality_id',
        'long',
        'lat',
        'status',
        'sensor_type',
        'created_at',
        'updated_at',
        'deleted_at',
        'synced_at',
    ];

    public function river()
    {
        return $this->belongsTo(River::class, 'river_id');
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'municipality_id');
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($sensor) {
            $sensor->threshold()->delete();
        });
    }

    public function threshold()
    {
        return $this->morphOne(Threshold::class, 'sensorable');
    }
}
