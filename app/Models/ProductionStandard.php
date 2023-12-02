<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionStandard extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string, double>
     */
    protected $fillable = [
        'production_schema_id',             //production schema for which production standard is set
        'component_id',                     //id of component bonded with production schema
        'product_id',                       //if production standard is determined directly for prod_schema and product
        'name',
        'duration_hours',                   //examplary duration - for instance 1 hour
        'amount',                           //amount that should be produced in exemplary duration
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
    protected $table = 'production_standard';
}
