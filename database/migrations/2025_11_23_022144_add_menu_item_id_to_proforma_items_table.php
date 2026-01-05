<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proforma_items', function (Blueprint $table) {
            $table->foreignId('menu_item_id')
                ->nullable()
                ->constrained('menu_items') // Cambiar de 'items' a 'menu_items'
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('proforma_items', function (Blueprint $table) {
            $table->dropForeign(['menu_item_id']);
            $table->dropColumn('menu_item_id');
        });
    }
};
