<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReasonCode extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string,>
     */
    protected $fillable = [
        'reason_code',                      //number that helps to identify the reason for defect creation
        'description',
    ];



}
