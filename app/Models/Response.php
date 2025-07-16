<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;

class Response extends Model
{
    use HasFactory;
    use UsesUuid;
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'color',
        'action',
        'code',
        'created_at',
        'updated_at',
        'synced_at',
    ];
}
