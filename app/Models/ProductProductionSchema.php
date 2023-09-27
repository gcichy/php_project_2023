<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductProductionSchema extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string, double>
     */
    protected $fillable = [
        'product_id',
        'production_schema_id',              //id of production schema used to craft the product
        'sequence_no',                      //order in which production_schema is used while poduct manufacturing
        'unit_id',                          //unit for the final product created basing on a certain schema (pcs etc.)
    ];
}
