<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeSaleItemsForeignKeyToCascade extends Migration
{
    public function up()
    {
        Schema::table('sale_items', function (Blueprint $table) {
            // Eliminar la foreign key existente
            $table->dropForeign(['menu_item_id']);

            // Volver a crearla con CASCADE
            $table->foreign('menu_item_id')
                ->references('id')
                ->on('menu_items')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropForeign(['menu_item_id']);

            $table->foreign('menu_item_id')
                ->references('id')
                ->on('menu_items')
                ->onDelete('restrict');
        });
    }
}
