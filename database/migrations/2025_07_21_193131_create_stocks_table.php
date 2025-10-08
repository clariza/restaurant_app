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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('menu_items');
            $table->foreignId('purchase_id')->constrained('purchases');
            $table->integer('quantity')->default(0);
            $table->decimal('unit_cost', 10, 2);
            $table->decimal('discount', 5, 2)->default(0);
            $table->decimal('total_cost', 10, 2);
            $table->decimal('selling_price', 10, 2);
            $table->date('expiry_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
