<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            
            // Relación correcta con menu_items
            $table->unsignedBigInteger('menu_item_id');
            $table->foreign('menu_item_id')
                  ->references('id')
                  ->on('menu_items')
                  ->onDelete('cascade');
            
            // // Relación correcta con users
            // $table->unsignedBigInteger('user_id');
            // $table->foreign('user_id')
            //       ->references('id')
            //       ->on('users')
            //       ->onDelete('cascade');
            
            // Campos del movimiento
            $table->enum('movement_type', ['addition', 'subtraction']);
            $table->decimal('quantity', 10, 2);
            $table->decimal('old_stock', 10, 2);
            $table->decimal('new_stock', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Forzar motor InnoDB (requerido para claves foráneas)
            $table->engine = 'InnoDB';
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory_movements');
    }
};