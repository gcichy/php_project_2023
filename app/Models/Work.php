<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string, double>
     */
    protected $fillable = [
        'user_id',                          //id of user reporting the job of himself
        'shift_id',                         //id of shift when user worked
        'task_id',                          //id of completed task
        'product_id',                       //id of product that the user was working on, optional if component doesn't demand product
        'component_id',                     //id of component that the user was working on
        'production_cycle_id',              //id of started production cycle for which the work is submitted
        'duration_minute',                  //working time
        'amount',                           //is necessary, result of work determined in related task's unit
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
