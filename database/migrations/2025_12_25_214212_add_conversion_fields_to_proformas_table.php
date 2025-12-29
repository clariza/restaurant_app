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
            // Campo para relacionar con la orden creada
            $table->unsignedBigInteger('order_id')->nullable()->after('total');
            $table->timestamp('converted_at')->nullable()->after('order_id');

            // Índices
            $table->index('order_id');
            $table->index('converted_at');

            // Relación foránea (opcional, depende de tu estructura)
            // $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proformas', function (Blueprint $table) {
            // Eliminar foreign key si se creó
            // $table->dropForeign(['order_id']);

            $table->dropIndex(['order_id']);
            $table->dropIndex(['converted_at']);
            $table->dropColumn(['order_id', 'converted_at']);
        });
    }
};
