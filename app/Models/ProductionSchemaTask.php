<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionSchemaTask extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string, double>
     */
    protected $fillable = [
        'production_schema_id',
        'task_id',                          //id of task performed in production_schema
        'sequence_no',                      //determines task execution order within production schema
        'amount_required',                  //determines if reporting task requires specifying work results
        'additional_description'            //enable to add description to task of certain production_schema
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'production_schema_task';

}
