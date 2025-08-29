<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('plans', function (Blueprint $t) {
            $t->id();
            $t->unsignedDecimal('budget', 8, 2)->default(0);
            $t->unsignedDecimal('total', 8, 2)->default(0);
            $t->unsignedDecimal('saving', 8, 2)->default(0);
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('plans');
    }
};

