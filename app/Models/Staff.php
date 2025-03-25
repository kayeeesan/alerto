<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $fillable = [
       'username',
        'first_name',
        'last_name',
        'middle_name',
        'mobile_number',
        'role_id',
        'government_agency',
        'region_id',
        'province_id',
        'municipality_id',
        'river_id',
        'lgu_fb',
        'status',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    public function river()
    {
        return $this->belongsTo(River::class);
    }
}
