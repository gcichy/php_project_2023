<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string, double>
     */
    protected $fillable = [
        'firstName',
        'lastName',
        'employeeNo',
        'role',
        'phoneNr',
        'email',
        'password',
        'salary',
        'isVerified',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string, boolean>
     */
    protected $hidden = [
        'id',
        'password',
        'rememberToken',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'emailVerifiedAt' => 'datetime',
    ];
}
