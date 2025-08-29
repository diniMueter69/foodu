<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('shopping_list_items', function (Blueprint $t) {
            $t->boolean('checked')->default(false)->after('position');
            $t->index(['shopping_list_id','checked']);
        });
    }
    public function down(): void
    {
        Schema::table('shopping_list_items', function (Blueprint $t) {
            $t->dropIndex(['shopping_list_id','checked']);
            $t->dropColumn('checked');
        });
    }
};
