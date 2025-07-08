<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;

class Province extends Model
{
    use HasFactory;
    use SoftDeletes; // Enable SoftDeletes
    use UsesUuid;

    protected $fillable = [
        'name',
        'region_id'
    ];

    public function region(){
        return $this->belongsTo(Region::class,'region_id');
    }
}
