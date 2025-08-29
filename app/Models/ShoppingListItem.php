<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingListItem extends Model
{
    protected $fillable = ['shopping_list_id','name','qty','price','store','position','checked'];
    protected $casts = ['checked'=>'boolean','price'=>'decimal:2'];


    public function list()
    {
        return $this->belongsTo(ShoppingList::class, 'shopping_list_id');
    }
}
