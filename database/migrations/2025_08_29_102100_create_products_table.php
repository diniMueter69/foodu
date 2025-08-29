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
        Schema::create('products', function (Blueprint $t) {
            $t->id(); 
            $t->string('name'); 
            $t->string('unit')->default('g'); 
            $t->unsignedInteger('size')->default(0); // z.B. 500
            $t->timestamps(); 
            $t->index('name');
        });
    } 
    
    public function down(): void 
    { 
        Schema::dropIfExists('products'); 
    } 
};