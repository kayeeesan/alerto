<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;

class Threshold extends Model
{
    use HasFactory;
    use UsesUuid;

    protected $fillable = [
        'sensorable_id',
        'sensorable_type',
        'baseline',
        'sixty_percent',
        'eighty_percent',
        'one_hundred_percent',
        'xs_date'
    ];

    public function sensorable()
    {
        return $this->morphTo();
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }
}
