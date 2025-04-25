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
        Schema::table('proformas', function (Blueprint $table) {
            $table->boolean('converted_to_order')->default(false);
            $table->foreignId('converted_order_id')
                  ->nullable()
                  ->constrained('sales')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proformas', function (Blueprint $table) {
            $table->dropForeign(['converted_order_id']);
            $table->dropColumn(['converted_to_order', 'converted_order_id']);
        });
    }
};
