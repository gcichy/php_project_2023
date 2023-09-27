<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instruction extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string, double>
     */
    protected $fillable = [
        'product_id',                       //if provided instruction is made for product
        'component_id',                     //if provided instruction is made for component
        'task_id',                          //if provided instruction is made for task
        'name',
        'instruction_html',                 //html text of instruction
        'instruction_image',                //image file name of instruction
        'video',
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
