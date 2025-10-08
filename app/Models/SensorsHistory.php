<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\UsesUuid;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

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

    protected static function triggerSync()
    {
        if (app()->runningInConsole()) return;

        if (!Cache::has('sync_recently_triggered')) {
            Cache::put('sync_recently_triggered', true, 10);
            Artisan::call('sync:main', ['model' => 'sensors_history']);
        }
    }

    public static function booted()
    {
        parent::boot();

        static::saved(fn() => self::triggerSync()); 
        static::deleted(fn() => self::triggerSync());
    }
}
