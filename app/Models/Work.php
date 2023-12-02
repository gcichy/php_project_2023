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
        'production_cycle_id',              //id of started production cycle for which the work is submitted
        'task_id',                          //id of completed task
        'production_schema_id',             //id of production schema reported task belongs to
        'product_id',                       //id of product that the user was working on, optional if component doesn't demand product
        'component_id',                     //id of component that the user was working on, optional if production schema doesn't demand component
        'start_time',
        'end_time',
        'duration_minute',                  //working time
        'amount',                           //if necessary, result of work determined in prod_schema related unit
        'defect_amount',                    //amount of defects produced, unit of defect can differ from prod_schema related unit
        'reason_code',
        'defect_unit_id',
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
