<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class Threshold extends Model
{
    use HasFactory;
    use UsesUuid;
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'sensorable_id',
        'sensorable_type',
        'baseline',
        'sixty_percent',
        'eighty_percent',
        'one_hundred_percent',
        'xs_date',
        'created_at',
        'updated_at',
        'synced_at',
        'deleted_at',
    ];

    public function sensorable()
    {
        return $this->morphTo();
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }

    protected static function triggerSync()
    {
        if (app()->runningInConsole()) return;

        if (!Cache::has('sync_recently_triggered')) {
            Cache::put('sync_recently_triggered', true, 10);
            Artisan::call('sync:main', ['model' => 'thresholds']);
        }
    }

    public static function booted()
    {
        parent::boot();

        static::saved(fn() => self::triggerSync()); 
        static::deleted(fn() => self::triggerSync());
    }
}
