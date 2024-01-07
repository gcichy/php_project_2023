<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionSchema extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string, double>
     */
    protected $fillable = [
        'production_schema',
        'description',
        'non_countable',                        //determines if prod cycle and prod standard can be created for prod schema
        'tasks_count',                      //number of tasks for production schema
        'waste_unit_id',                        //id of waste unit produced during production process for prod schema
    ];
    //independency of production_schema is determined by having row in production_standard table where component_id is null
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
    protected $table = 'production_schema';


}
