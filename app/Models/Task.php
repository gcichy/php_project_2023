<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string,>
     */
    protected $fillable = [
        'production_schema_id',                  //id of production schema that the task is assigned to
        'name',
        'sequence_no',                          //determines execution order within production schema
        'amount_required',                      //determines is amount must be supplied when reporting task
        'description',
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
    protected $table = 'task';
}
