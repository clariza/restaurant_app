<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('petty_cash_id')->nullable()->after('id'); // Clave foránea
            //$table->foreign('petty_cash_id')->references('id')->on('petty_cashes')->onDelete('set null');
        });
    }
    
    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
           // $table->dropForeign(['petty_cash_id']); // Eliminar la restricción de clave foránea
            $table->dropColumn('petty_cash_id'); // Eliminar la columna
        });
    }
};
