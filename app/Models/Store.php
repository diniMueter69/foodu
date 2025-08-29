<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = ['code','name'];

    public function items()
    {
        return $this->hasMany(StoreProduct::class);
    }
}
