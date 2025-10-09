<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
               // Verificar y eliminar columnas antiguas solo si existen
            if (Schema::hasColumn('purchases', 'product')) {
                $table->dropColumn('product');
            }
            if (Schema::hasColumn('purchases', 'price')) {
                $table->dropColumn('price');
            }
            if (Schema::hasColumn('purchases', 'quantity')) {
                $table->dropColumn('quantity');
            }
            
            // Agregar las nuevas columnas si no existen
            if (!Schema::hasColumn('purchases', 'reference_number')) {
                $table->string('reference_number')->nullable()->after('supplier_id');
            }
            if (!Schema::hasColumn('purchases', 'purchase_date')) {
                $table->dateTime('purchase_date')->after('reference_number');
            }
            if (!Schema::hasColumn('purchases', 'total_amount')) {
                $table->decimal('total_amount', 10, 2)->after('purchase_date');
            }
            if (!Schema::hasColumn('purchases', 'status')) {
                $table->string('status')->default('completed')->after('total_amount');
            }
        
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            // Eliminar las nuevas columnas si existen
            $table->dropColumn(['reference_number', 'purchase_date', 'total_amount', 'status']);
            
            // Volver a agregar las columnas antiguas
            $table->string('product')->after('supplier_id');
            $table->decimal('price', 8, 2)->after('product');
            $table->integer('quantity')->after('price');
        });
    }
};
