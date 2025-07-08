<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;

class Notification extends Model
{
    use HasFactory;
    use UsesUuid;

    protected $fillable = [
        'user_id',
        'river_id',
        'text',
        'type',
        'read_at',
        'seen_at',
        'alert_id'
    ];

    protected $dates = [
        'read_at',
        'seen_at',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function river()
    {
        return $this->belongsTo(River::class, 'river_id');
    }

    public function alert()
    {
        return $this->belongsTo(Alert::class, 'alert_id');
    }
}
