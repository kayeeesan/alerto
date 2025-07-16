<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;

class Region extends Model
{
    use HasFactory;
    use UsesUuid;

    protected $fillable = [
        'uuid',
        'name',
        'created_at',
        'updated_at',
        'synced_at',
    ];
}
