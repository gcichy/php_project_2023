<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkEffectivity extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string, double>
     */
    protected $fillable = [
        'production_cycle_schema_id',
        'production_standard_id',
        'duration_minute',
        'productivity',                     //ratio amount created/standard amount
        'amount',                           //produced amount
        'finished',                         //if not finished work for prod_cycle is reported for this work_effectivity
        //other performance measurements
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
    protected $table = 'work_effectivity';

}
