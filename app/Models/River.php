<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;

class River extends Model
{
    use HasFactory;
    use SoftDeletes; // Enable SoftDeletes
    use UsesUuid;

    protected $fillable = [
        'uuid',
        'name',
        'river_code',
        'municipality_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'synced_at', 
    ];

    public function municipality(){
        return $this->belongsTo(Municipality::class,'municipality_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
