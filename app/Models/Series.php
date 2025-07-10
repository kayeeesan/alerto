<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;

class Series extends Model
{
    use HasFactory;
    use UsesUuid;
    
    protected $fillable = [ 
        'starting_value'
    ]; 
}
