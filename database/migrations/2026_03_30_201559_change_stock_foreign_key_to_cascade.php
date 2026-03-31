<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeStockForeignKeyToCascade extends Migration
{
    public function up()
    {
        Schema::table('stocks', function (Blueprint $table) {
            // Eliminar la foreign key existente
            $table->dropForeign(['product_id']);

            // Volver a crearla con CASCADE
            $table->foreign('product_id')
                ->references('id')
                ->on('menu_items')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('stock', function (Blueprint $table) {
            $table->dropForeign(['product_id']);

            $table->foreign('product_id')
                ->references('id')
                ->on('menu_items')
                ->onDelete('restrict');
        });
    }
}
