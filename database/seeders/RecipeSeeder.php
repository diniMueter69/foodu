<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recipe;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        Recipe::upsert(
            [
                ['id'=>1,'title'=>'Pasta Aglio e Olio','price_per_portion'=>1.85,'time'=>15,'kcal'=>520],
                ['id'=>2,'title'=>'Gemuese-Pfanne mit Reis','price_per_portion'=>2.10,'time'=>20,'kcal'=>480],
            ],
            ['id'], // unique by
            ['title','price_per_portion','time','kcal'] // diese Felder bei Konflikt updaten
        );
    }
}
