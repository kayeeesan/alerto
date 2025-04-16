<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'staffs'; 
    use HasFactory;

    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'mobile_number',
        'role_id',
        'region_id',
        'province_id',
        'municipality_id',
        'river_id',
        'fb_lgu',
        'status'
    ];

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
