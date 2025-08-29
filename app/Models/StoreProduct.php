<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreProduct extends Model
{
    protected $fillable = ['store_id','product_id','ean','pack_size','pack_unit'];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function snapshots()
    {
        return $this->hasMany(PriceSnapshot::class);
    }
}
