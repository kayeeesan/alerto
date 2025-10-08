<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class Staff extends Model
{
    protected $table = 'staffs'; 
    use HasFactory;
    use UsesUuid;
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'mobile_number',
        'role_id',
        'region_id',
        'province_id',
        'municipality_id',
        'river_id',
        'fb_lgu',
        'created_at',
        'updated_at',
        'deleted_at',
        'synced_at', 
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function role(){
        return $this->belongsTo(Role::class,'role_id');
    }

    public function region(){
        return $this->belongsTo(Region::class,'region_id');
    }

    public function province(){
        return $this->belongsTo(Province::class,'province_id');
    }

    public function municipality(){
        return $this->belongsTo(Municipality::class, 'municipality_id');
    }

    public function river(){
        return $this->belongsTo(River::class, 'river_id');
    }

    protected static function triggerSync()
    {
        if (app()->runningInConsole()) return;

        if (!Cache::has('sync_recently_triggered')) {
            Cache::put('sync_recently_triggered', true, 10);
            Artisan::call('sync:main', ['model' => 'staffs']);
        }
    }

    public static function booted()
    {
        parent::boot();

        static::saved(fn() => self::triggerSync()); 
        static::deleted(fn() => self::triggerSync());
    }
}
