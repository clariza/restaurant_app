<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('denominaciones', function (Blueprint $table) {
            $table->id(); // Columna autoincremental para el ID
            $table->decimal('valor', 8, 2); // Valor de la denominación (ej: 1000, 500, 200, etc.)
            $table->integer('cantidad');    // Cantidad de billetes/monedas de esta denominación
            $table->timestamps();           // Columnas created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('denominaciones'); // Eliminar la tabla si se revierte la migración
    }
};
