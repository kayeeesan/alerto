<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;

class Role extends Model
{
    use HasFactory;
    use UsesUuid;

    protected $fillable = [
        'name',
        'slug',
    ];

    public function users() {
        return $this->belongsToMany(User::class, 'user_roles', 'role_id', 'user_id');
    }
}
