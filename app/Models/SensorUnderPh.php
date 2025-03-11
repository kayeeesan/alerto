<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SensorUnderPh extends Model
{
    use HasFactory; 
    use SoftDeletes; // Enable SoftDeletes

    protected $table = 'sensors_under_phs'; 

    protected $fillable = [
        'name',
        'baseline',
        'sixty_percent',
        'eighty_percent',
        'one_hundred_percent',
    ];
}
