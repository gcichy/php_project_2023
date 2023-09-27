<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComponentProductionSchema extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string, double>
     */
    protected $fillable = [
        'component_id',
        'production_schema_id',              //id of production schema used to craft the component
        'sequence_no',                      //order in which production_schema is used while component manufacturing
        'unit_id',                          //unit for the final product created basing on a certain schema (pcs etc.)
    ];
}
