<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\HasRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;

class User extends Authenticatable
{
    use HasRole;
    use HasFactory, Notifiable, HasApiTokens;
    use UsesUuid;
    use SoftDeletes;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'username',
        'first_name',
        'last_name',
        'middle_name',
        'password',
        'status',
        'created_at',
        'updated_at',
        'synced_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            // 'password' => 'hashed',
        ];
    }

    public function getFullnameAttribute()
    {
        $first_name = ucfirst($this->first_name);
        $middle_name = ucfirst($this->middle_name);
        $last_name = ucfirst($this->last_name);

        return "{$last_name}, {$first_name} {$middle_name}";
    }

    public function staff()
    {
        return $this->hasOne(Staff::class, 'user_id');
    }

    public function getRiverAttribute()
    {
        return $this->staff?->river;
    }


}
