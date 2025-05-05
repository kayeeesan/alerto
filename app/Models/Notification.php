<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'river_id',
        'text',
        'type',
        'read_at',
        'seen_at'
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
}
