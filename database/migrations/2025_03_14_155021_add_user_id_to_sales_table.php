<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id'); // Agregar campo user_id
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null'); // Clave foránea
        });
    }

    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['user_id']); // Eliminar clave foránea
            $table->dropColumn('user_id'); // Eliminar columna
        });
    }
};
