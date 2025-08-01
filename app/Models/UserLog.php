<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;

class UserLog extends Model
{
    use HasFactory; 
    use UsesUuid;
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'entity_type',
        'entity_id',
        'action',
        'changes',
        'created_at',
        'updated_at',
        'synced_at',
    ];

    protected $casts = [
        'changes' => 'array', 
    ];
}
