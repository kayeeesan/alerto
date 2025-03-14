<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{
    use HasFactory; 

    protected $fillable = [
        'user_id',
        'entity_type',
        'entity_id',
        'action',
        'changes'
    ];

    protected $casts = [
        'changes' => 'array', // Automatically converts JSON to array
    ];
}
