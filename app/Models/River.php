<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class River extends Model
{
    use HasFactory;
    use SoftDeletes; // Enable SoftDeletes

    protected $fillable = [
        'name',
        'river_code',
        'municipality_id'
    ];

    public function municipality(){
        return $this->belongsTo(Municipality::class,'municipality_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
