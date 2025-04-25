<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('petty_cash', function (Blueprint $table) {
        // Agregar los nuevos campos
        $table->decimal('total_sales_cash', 10, 2)->default(0)->after('current_amount');
        $table->decimal('total_sales_qr', 10, 2)->default(0)->after('total_sales_cash');
        $table->decimal('total_sales_card', 10, 2)->default(0)->after('total_sales_qr');
        $table->decimal('total_expenses', 10, 2)->default(0)->after('total_sales_card');
        $table->decimal('total_general', 10, 2)->default(0)->after('total_expenses');
        $table->timestamp('closed_at')->nullable()->after('total_expenses');
    });
}

public function down()
{
    Schema::table('petty_cash', function (Blueprint $table) {
        // Revertir los cambios en caso de rollback
        $table->dropColumn('total_sales_cash');
        $table->dropColumn('total_sales_qr');
        $table->dropColumn('total_sales_card');
        $table->dropColumn('total_expenses');
        $table->dropColumn('total_general');
        $table->dropColumn('closed_at');
    });
}
};
