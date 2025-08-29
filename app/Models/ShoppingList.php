<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingList extends Model
{
    protected $fillable = ['strategy'];

    public function items()
    {
        return $this->hasMany(ShoppingListItem::class);
    }
}
