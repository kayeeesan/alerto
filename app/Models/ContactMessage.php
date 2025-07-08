<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;

class ContactMessage extends Model
{
    use HasFactory;
    use UsesUuid;

    protected $fillable = [
        'name',
        'email',
        'contact_number',
        'message'
    ];
}
