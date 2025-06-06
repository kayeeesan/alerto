<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Province extends Model
{
    use HasFactory;
    use SoftDeletes; // Enable SoftDeletes

    protected $fillable = [
        'name',
        'region_id'
    ];

    public function region(){
        return $this->belongsTo(Region::class,'region_id');
    }
}
