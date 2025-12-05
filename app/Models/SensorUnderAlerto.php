<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class SensorUnderAlerto extends Model
{
    use HasFactory;
    use SoftDeletes; // Enable SoftDeletes
    use UsesUuid;

    protected $table = 'sensors_under_alerto'; 

    protected $fillable = [
        'uuid',
        'name',
        'device_id',
        'device_rain_amount',
        'device_water_level',
        'previous_water_level',
        'previous_rain_amount',  
        'total_acc',
        'event_acc',
        'recent_acc',
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
        'api_last_updated_at'
    ];

    public function river()
    {
        return $this->belongsTo(River::class,'river_id');
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'municipality_id');
    }

    public static function booted()
    {
        parent::boot();

        static::deleting(function ($sensor) {
            $sensor->threshold()->delete();
        });

        static::saved(fn() => self::triggerSync()); 
        static::deleted(fn() => self::triggerSync());
    }

    public function threshold()
    {
        return $this->morphOne(Threshold::class, 'sensorable');
    }

    protected static function triggerSync()
    {
        if (app()->runningInConsole()) return;

        if (!Cache::has('sync_recently_triggered')) {
            Cache::put('sync_recently_triggered', true, 10);
            Artisan::call('sync:main', ['model' => 'sensors_under_alerto']);
        }
    }

    
}
