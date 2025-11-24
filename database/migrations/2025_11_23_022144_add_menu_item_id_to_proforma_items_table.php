<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('proforma_items', function (Blueprint $table) {
            $table->unsignedBigInteger('menu_item_id')->nullable()->after('proforma_id');
            $table->foreign('menu_item_id')->references('id')->on('items')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('proforma_items', function (Blueprint $table) {
            $table->dropForeign(['menu_item_id']);
            $table->dropColumn('menu_item_id');
        });
    }
};
