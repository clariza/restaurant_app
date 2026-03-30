<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch_menu_item_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')
                  ->constrained('branches')
                  ->onDelete('cascade');
            $table->foreignId('menu_item_id')
                  ->constrained('menu_items')
                  ->onDelete('cascade');
            $table->decimal('stock', 10, 2)->default(0);
            $table->decimal('min_stock', 10, 2)->default(5);
            $table->timestamps();

            $table->unique(['branch_id', 'menu_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_menu_item_stock');
    }
};