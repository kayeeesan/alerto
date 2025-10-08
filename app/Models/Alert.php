<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class Alert extends Model
{
    use HasFactory;
    use UsesUuid;
    use SoftDeletes;
    
    protected $fillable = [
        'uuid',
        'threshold_id',
        'response_id',
        'details',
        'status',
        'expired_at',
        'user_id',
        'type',
        'created_at',
        'updated_at',
        'synced_at',
        'deleted_at',
    ];

    public function threshold()
    {
        return $this->belongsTo(Threshold::class);
    }

    public function response()
    {
        return $this->belongsTo(Response::class, 'response_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeExpiredPending($query)
    {
        return $query->where('status', 'pending')
                    ->where('expired_at', '<=', now());
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'alert_id');
    }


    public function users()
    {
        return $this->belongsToMany(User::class, 'alert_user', 'alert_id', 'user_id')
                    ->withTimestamps();
    }

    protected static function triggerSync()
    {
        if (app()->runningInConsole()) return;

        if (!Cache::has('sync_recently_triggered')) {
            Cache::put('sync_recently_triggered', true, 10);
            Artisan::call('sync:main', ['model' => 'alerts']);
        }
    }

    public static function booted()
    {
        parent::boot();

        static::saved(fn() => self::triggerSync()); 
        static::deleted(fn() => self::triggerSync());
    }

}
