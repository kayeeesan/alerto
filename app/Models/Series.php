<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;

class Series extends Model
{
    use HasFactory;
    use UsesUuid;
    use SoftDeletes;
    
    protected $fillable = [ 
        'uuid',
        'name',
        'slug',
        'prefix',
        'current_date',
        'starting_value',
        'max_digit',
        'created_at',
        'updated_at',
        'synced_at'
    ]; 
}
