<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string, double>
     */
    protected $fillable = [
        'gtin',
        'name',
        'material',
        'color',
        'description',
        'image',
        'barcode_image',
        'price',
        'piecework_fee'                 //fee for one manufactured pcs of product
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
    protected $table = 'product';
}
