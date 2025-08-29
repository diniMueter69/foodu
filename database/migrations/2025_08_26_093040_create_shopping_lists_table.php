<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shopping_lists', function (Blueprint $t) {
            $t->id();
            $t->string('strategy')->default('best_price');
            $t->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('shopping_lists');
    }
};
