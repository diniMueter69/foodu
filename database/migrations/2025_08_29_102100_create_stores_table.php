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
          Schema::create('stores', function (Blueprint $t) { 
            $t->id(); 
            $t->string('code')->unique(); 
            $t->string('name'); 
            $t->timestamps(); });

    }   
    
    
    public function down(): void 
    { 
    Schema::dropIfExists('stores'); 
    } 
};