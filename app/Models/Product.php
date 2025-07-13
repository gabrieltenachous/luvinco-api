<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'product_id',
        'name',
        'description',
        'price',
        'category',
        'brand',
        'stock',
        'image_url',
    ];
}