<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->unsignedDecimal('price_per_portion', 6, 2); // kein Minus, mehr Spielraum
            $t->unsignedSmallInteger('time');
            $t->unsignedSmallInteger('kcal');
            $t->timestamps();

            $t->index('title'); // optionaler Index für Suche/Sortierung
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
