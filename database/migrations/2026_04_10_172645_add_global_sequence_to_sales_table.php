<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Agregar columna para el número global incremental
        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('order_sequence')->nullable()->unique()->after('transaction_number');
        });

        // 2. Poblar el sequence en registros existentes ordenados por id
        $sales = DB::table('sales')->orderBy('id')->get();
        foreach ($sales as $index => $sale) {
            DB::table('sales')
                ->where('id', $sale->id)
                ->update(['order_sequence' => $index + 1]);
        }
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('order_sequence');
        });
    }
};