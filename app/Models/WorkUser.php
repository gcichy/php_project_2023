<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkUser extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string, double>
     */
    protected $fillable = [
        'work_id',
        'user_id',
        'duration_minute_per_user',         //working time per user
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'work_user';

}
