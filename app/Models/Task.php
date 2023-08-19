<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string,>
     */
    protected $fillable = [
        'taskDescription',
        'productID',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int>
     */
    protected $hidden = [
        'id',
    ];

}
