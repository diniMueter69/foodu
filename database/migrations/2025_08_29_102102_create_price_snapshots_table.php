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
        Schema::create('price_snapshots', function (Blueprint $t) 
        {
            $t->id(); $t->foreignId('store_product_id')->constrained()->cascadeOnDelete();
            $t->string('region')->nullable(); 
            $t->unsignedDecimal('price',6,2);
            $t->timestamp('captured_at')->index(); 
            $t->timestamps();
            $t->index(['store_product_id','captured_at']);
        });
    } 

    public function down(): void 
    { 
        Schema::dropIfExists('price_snapshots'); 
    } 
};