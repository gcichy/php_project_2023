<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Defect extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string,>
     */
    protected $fillable = [
        'work_id',
        'amount',
        'reason_code',
        'additional_comment'
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
