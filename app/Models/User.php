<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'user_id',        // NIP / userId dari UIMNG
        'name',
        'email',
        'password',
        'role_id',
        'unit_kerja_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    // PENTING:
    // JANGAN override getAuthIdentifierName() ke user_id
    // biarkan default = 'id' supaya sessions.user_id tetap angka (bigint).
}
