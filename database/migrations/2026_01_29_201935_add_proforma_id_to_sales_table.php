<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // Verificar y agregar 'proforma_id' solo si no existe
            if (!Schema::hasColumn('sales', 'proforma_id')) {
                $table->unsignedBigInteger('proforma_id')->nullable()->after('petty_cash_id');
            }
        });

        // Agregar índice solo si no existe
        $indexes = DB::select("SHOW INDEX FROM sales WHERE Key_name = 'sales_proforma_id_index'");
        if (empty($indexes)) {
            Schema::table('sales', function (Blueprint $table) {
                $table->index('proforma_id');
            });
        }

        // Agregar foreign key solo si no existe
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'sales' 
            AND CONSTRAINT_NAME = 'sales_proforma_id_foreign'
        ");

        if (empty($foreignKeys)) {
            Schema::table('sales', function (Blueprint $table) {
                $table->foreign('proforma_id')
                    ->references('id')
                    ->on('proformas')
                    ->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // Verificar y eliminar foreign key si existe
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'sales' 
                AND CONSTRAINT_NAME = 'sales_proforma_id_foreign'
            ");

            if (!empty($foreignKeys)) {
                $table->dropForeign(['proforma_id']);
            }

            // Verificar y eliminar índice si existe
            $indexes = DB::select("SHOW INDEX FROM sales WHERE Key_name = 'sales_proforma_id_index'");
            if (!empty($indexes)) {
                $table->dropIndex(['proforma_id']);
            }

            // Verificar y eliminar columna si existe
            if (Schema::hasColumn('sales', 'proforma_id')) {
                $table->dropColumn('proforma_id');
            }
        });
    }
};
