<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shopping_list_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('shopping_list_id')->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->unsignedSmallInteger('qty')->default(1);
            $t->unsignedDecimal('price', 6, 2); // CHF xx.yy
            $t->string('store', 60)->nullable();
            $t->unsignedSmallInteger('position')->default(0);
            $t->timestamps();

            $t->index(['shopping_list_id','position']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('shopping_list_items');
    }
};
