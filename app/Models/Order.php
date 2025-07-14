<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory,HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [ 
        'status',
    ]; 
    
    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }

}
