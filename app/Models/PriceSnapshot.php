<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceSnapshot extends Model
{
    protected $fillable = ['store_product_id','region','price','captured_at'];

    protected $casts = [
        'price' => 'decimal:2',
        'captured_at' => 'datetime',
    ];
}
