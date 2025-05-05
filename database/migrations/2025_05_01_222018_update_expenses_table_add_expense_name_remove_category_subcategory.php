<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['category', 'subcategory']);
            
            // Agregar la nueva columna expense_name
            $table->string('expense_name')->after('id');
        });
    }

    public function down()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->string('category')->nullable();
            $table->string('subcategory')->nullable();
            $table->dropColumn('expense_name');
        });
    }
};
