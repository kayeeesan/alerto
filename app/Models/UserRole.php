<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class UserRole extends Model
{
    use HasFactory;

    public $timestamps = true;
    public $incrementing = false;
    public $primaryKey = null;
    protected $table = 'user_roles';

    protected $fillable = [
        'user_id',
        'role_id',
        'created_at',
        'updated_at',
        'synced_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    protected static function triggerSync()
    {
        if (app()->runningInConsole()) return;

        if (!Cache::has('sync_recently_triggered_user_roles')) {
            Cache::put('sync_recently_triggered_user_roles', true, 10);
            Artisan::call('sync:main', ['model' => 'user_roles']);
        }
    }

    public static function booted()
    {
        parent::boot();

        static::saved(fn() => self::triggerSync()); 
        static::deleted(fn() => self::triggerSync());
    }
}
