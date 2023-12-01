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
        'production_cycle_id',              //id of started production cycle for which the work is submitted
        'task_id',                          //id of completed task
        'production_schema_id',             //id of production schema reported task belongs to
        'product_id',                       //id of product that the user was working on, optional if component doesn't demand product
        'component_id',                     //id of component that the user was working on, optional if production schema doesn't demand component
        'start_time',
        'end_time',
        'duration_minute',                  //working time
        'amount',                           //if necessary, result of work determined in related task's unit
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

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'work';
}
