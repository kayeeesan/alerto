<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class Municipality extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UsesUuid;
    protected $fillable = [
        'uuid',
        'name',
        'province_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'synced_at',

    ];

    public function province()
    {
        return $this->belongsTo(Province::class,'province_id' );
    }

    protected static function triggerSync()
    {
        if (app()->runningInConsole()) return;

        if (!Cache::has('sync_recently_triggered')) {
            Cache::put('sync_recently_triggered', true, 10);
            Artisan::call('sync:main', ['model' => 'municipalities']);
        }
    }

    public static function booted()
    {
        parent::boot();

        static::saved(fn() => self::triggerSync()); 
        static::deleted(fn() => self::triggerSync());
    }
}
