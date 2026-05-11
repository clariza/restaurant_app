<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('menu_items', 'is_available')) {
            Schema::table('menu_items', function (Blueprint $table) {
                $table->boolean('is_available')->default(true)->change();
            });
        } else {
            Schema::table('menu_items', function (Blueprint $table) {
                $table->boolean('is_available')->default(true);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('menu_items', 'is_available')) {
            Schema::table('menu_items', function (Blueprint $table) {
                $table->boolean('is_available')->change();
            });
        }
    }
};
