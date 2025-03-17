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
    ];

    public function threshold()
    {
        return $this->belongsTo(Threshold::class);
    }

    public function response()
    {
        return $this->belongsTo(Response::class);
    }
}
