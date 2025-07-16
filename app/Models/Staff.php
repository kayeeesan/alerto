<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;

class Staff extends Model
{
    protected $table = 'staffs'; 
    use HasFactory;
    use UsesUuid;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'mobile_number',
        'role_id',
        'region_id',
        'province_id',
        'municipality_id',
        'river_id',
        'fb_lgu'
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
}
