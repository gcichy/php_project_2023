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
        'parent_id',                        //id of parent production schema
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
    protected $table = 'production_schema';


}
