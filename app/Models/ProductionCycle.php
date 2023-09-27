<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionCycle extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string, double>
     */
    protected $fillable = [
        'production_schema_id',             //id of production schema for which production cycle is created
        'product_id',                       //id of product for which production cycle is created
        'component_id',                     //if production cycle is created for component (not for product), then component id must be supplied
        'start_time',                       //cycle start time
        'end_time',                         //cycle end time
        'duration_minute_sum',              //summed duration of works from start to completion of production cycle
        'amount_sum',                       //summed amount for all works of this cycle
        'cycle_finished',                   //determines if cycle is finished
        'effectivity_calculated',           //determines if effectivity has been calculated for the finished cycle
        'additional_comment',
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
