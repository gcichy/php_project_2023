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
        'level',                            //determines hierarchy: 1 - parent (prod,comp,schema), 2 - child (comp, schema), 3 - subchild (schema)
        'category',                         //determines if cycle is for product, component or prod_schema
        'production_schema_id',             //id of production schema for which production cycle is created
        'product_id',                       //id of product for which production cycle is created
        'component_id',                     //if production cycle is created for component (not for product), then component id must be supplied
        'parent_id',                        //if cycle is created for product, then subcycles are created for its components and prod schemas
        'sequence_no',                      //if needed determines order of execution
        'start_time',                       //cycle real start time
        'end_time',                         //cycle real end time
        'expected_start_time',              //cycle expected start time - determined on creation
        'expected_end_time',                //cycle expected end time - determined on creation
        'duration_minute_sum',              //summed duration of works from start to completion of production cycle
        'total_amount',                     //total amount to be produced within cycle
        'current_amount',
        'defect_amount',                    //determines how many defects was produced
        'finished',                   //determines if cycle is finished
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

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'production_cycle';

}
