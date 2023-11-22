<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaticValue extends Model
{
    protected $fillable = [
        'type',
        'value',
        'value_full',
        'description',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int>
     */

    protected $hidden = [
        'id',
    ];


    protected $table = 'static_value';
}
