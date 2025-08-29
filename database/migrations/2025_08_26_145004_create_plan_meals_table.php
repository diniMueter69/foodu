<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('plan_meals', function (Blueprint $t) {
            $t->id();
            $t->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $t->foreignId('recipe_id')->constrained()->cascadeOnDelete();
            $t->unsignedSmallInteger('position')->default(0); // Reihenfolge 1..N
            $t->timestamps();
            $t->unique(['plan_id','position']);
            $t->index(['plan_id','recipe_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('plan_meals');
    }
};
