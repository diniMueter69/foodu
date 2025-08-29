<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name','unit','size'];

    public function offers()
    {
        return $this->hasMany(StoreProduct::class);
    }
}
