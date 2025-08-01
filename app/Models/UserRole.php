<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    use HasFactory;

    public $timestamps = true;
    public $incrementing = false;
    public $primaryKey = null;
    protected $table = 'user_roles';

    protected $fillable = [
        'user_id',
        'role_id',
        'created_at',
        'updated_at',
        'synced_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
