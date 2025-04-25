<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('petty_cash', function (Blueprint $table) {
            $table->id();
            $table->decimal('initial_amount', 10, 2); // Monto inicial de la caja chica
            $table->decimal('current_amount', 10, 2); // Monto actual de la caja chica
            $table->date('date'); // Fecha del cierre
            $table->text('notes')->nullable(); // Notas adicionales
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('petty_cash');
    }
};
