<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;

class Alert extends Model
{
    use HasFactory;
    use UsesUuid;
    
    protected $fillable = [
        'uuid',
        'threshold_id',
        'response_id',
        'details',
        'status',
        'expired_at',
        'user_id',
        'created_at',
        'updated_at',
        'synced_at',
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

}
