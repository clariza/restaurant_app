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
        Schema::table('proformas', function (Blueprint $table) {
            // Verificar y agregar 'is_converted' solo si no existe
            if (!Schema::hasColumn('proformas', 'is_converted')) {
                $table->boolean('is_converted')->default(false)->after('status');
            }

            // Verificar y agregar 'converted_order_id' solo si no existe
            if (!Schema::hasColumn('proformas', 'converted_order_id')) {
                $table->unsignedBigInteger('converted_order_id')->nullable()->after('is_converted');
            }

            // Verificar y agregar 'converted_at' solo si no existe
            if (!Schema::hasColumn('proformas', 'converted_at')) {
                $table->timestamp('converted_at')->nullable()->after('converted_order_id');
            }
        });

        // Agregar índices solo si no existen
        $indexes = DB::select("SHOW INDEX FROM proformas WHERE Key_name = 'proformas_is_converted_index'");
        if (empty($indexes)) {
            Schema::table('proformas', function (Blueprint $table) {
                $table->index('is_converted');
            });
        }

        $indexes = DB::select("SHOW INDEX FROM proformas WHERE Key_name = 'proformas_converted_order_id_index'");
        if (empty($indexes)) {
            Schema::table('proformas', function (Blueprint $table) {
                $table->index('converted_order_id');
            });
        }

        // Agregar foreign key solo si no existe
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'proformas' 
            AND CONSTRAINT_NAME = 'proformas_converted_order_id_foreign'
        ");

        if (empty($foreignKeys)) {
            Schema::table('proformas', function (Blueprint $table) {
                $table->foreign('converted_order_id')
                    ->references('id')
                    ->on('sales')
                    ->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proformas', function (Blueprint $table) {
            // Verificar y eliminar foreign key si existe
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'proformas' 
                AND CONSTRAINT_NAME = 'proformas_converted_order_id_foreign'
            ");

            if (!empty($foreignKeys)) {
                $table->dropForeign(['converted_order_id']);
            }

            // Verificar y eliminar índices si existen
            $indexes = DB::select("SHOW INDEX FROM proformas WHERE Key_name = 'proformas_is_converted_index'");
            if (!empty($indexes)) {
                $table->dropIndex(['is_converted']);
            }

            $indexes = DB::select("SHOW INDEX FROM proformas WHERE Key_name = 'proformas_converted_order_id_index'");
            if (!empty($indexes)) {
                $table->dropIndex(['converted_order_id']);
            }

            // Verificar y eliminar columnas si existen
            if (Schema::hasColumn('proformas', 'is_converted')) {
                $table->dropColumn('is_converted');
            }

            if (Schema::hasColumn('proformas', 'converted_order_id')) {
                $table->dropColumn('converted_order_id');
            }

            if (Schema::hasColumn('proformas', 'converted_at')) {
                $table->dropColumn('converted_at');
            }
        });
    }
};
