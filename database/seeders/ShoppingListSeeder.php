<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;

class ShoppingListSeeder extends Seeder
{
    public function run(): void
    {
        $list = ShoppingList::firstOrCreate(['id'=>1], ['strategy'=>'best_price']);

        $items = [
            ['name'=>'Spaghetti 500g', 'qty'=>1, 'price'=>1.20, 'store'=>'Denner', 'position'=>1],
            ['name'=>'Knoblauch',      'qty'=>1, 'price'=>0.50, 'store'=>'Lidl',   'position'=>2],
        ];

        foreach ($items as $i) {
            ShoppingListItem::updateOrCreate(
                ['shopping_list_id'=>$list->id, 'name'=>$i['name']],
                $i + ['shopping_list_id'=>$list->id]
            );
        }
    }
}
