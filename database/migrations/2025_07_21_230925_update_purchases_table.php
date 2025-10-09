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
            $table->string('reference_number')->nullable()->after('supplier_id');
            $table->decimal('total_amount', 10, 2)->after('purchase_date');
            $table->string('status')->default('completed')->after('total_amount');
            $table->dropColumn(['product', 'price', 'quantity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn(['reference_number', 'total_amount', 'status']);
            $table->string('product');
            $table->decimal('price', 8, 2);
            $table->integer('quantity');
        });
    }
};
