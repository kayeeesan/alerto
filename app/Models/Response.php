<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;

class Response extends Model
{
    use HasFactory;
    use UsesUuid;

    protected $fillable = [
        'color',
        'action',
        'code'
    ];
}
