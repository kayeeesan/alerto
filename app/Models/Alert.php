<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'threshold_id',
        'response_id',
        'details',
        'status',
        'expired_at',
        'user_id'
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

}
