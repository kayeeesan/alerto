<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;

class Municipality extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UsesUuid;
    protected $fillable = [
        'uuid',
        'name',
        'province_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'synced_at',

    ];

    public function province()
    {
        return $this->belongsTo(Province::class,'province_id' );
    }
}
