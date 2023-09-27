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
        'component_id',                       //id of component bonded with production schema
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
}
