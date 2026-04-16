<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migración opcional: renumera todos los transaction_number existentes
 * en orden cronológico (ORD-00001, ORD-00002, ...).
 *
 * ⚠️  Ejecutar solo si se desea limpiar los códigos aleatorios anteriores.
 *     Los números nuevos quedarán ordenados por created_at.
 *
 * php artisan migrate  (después de colocar este archivo en database/migrations/)
 */
return new class extends Migration
{
    public function up(): void
    {
        // Obtener todas las ventas ordenadas cronológicamente
        $sales = DB::table('sales')
            ->orderBy('created_at', 'asc')
            ->get(['id', 'transaction_number']);

        $counter = 1;
        foreach ($sales as $sale) {
            $newNumber = 'ORD-' . str_pad($counter, 5, '0', STR_PAD_LEFT);
            DB::table('sales')
                ->where('id', $sale->id)
                ->update(['transaction_number' => $newNumber]);
            $counter++;
        }
    }

    public function down(): void
    {
        // No es posible restaurar los números aleatorios originales.
        // Si necesitas hacer rollback, restaura desde un backup de la BD.
    }
};
