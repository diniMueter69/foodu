<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('store_products', function (Blueprint $t) {
            $t->id(); 
            $t->foreignId('store_id')->constrained()->cascadeOnDelete();
            $t->foreignId('product_id')->constrained()->cascadeOnDelete();
            $t->string('ean')->nullable(); 
            $t->unsignedInteger('pack_size')->default(0); 
            $t->string('pack_unit')->default('g');
            $t->timestamps(); 
            $t->unique(['store_id','product_id']);
        });
    } 

    public function down(): void 
    { 
        Schema::dropIfExists('store_products'); 
    } 
};

