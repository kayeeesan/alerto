<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SensorUnderAlerto extends Model
{
    use HasFactory;
    use SoftDeletes; // Enable SoftDeletes

    protected $table = 'sensors_under_alertos'; 

    protected $fillable = [
        'name',
        'river_id',
        'municipality_id',
        'long',
        'lat',
        'status'
    ];

    public function river()
    {
        return $this->belongsTo(River::class,'river_id');
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'municipality_id');
    }
}
