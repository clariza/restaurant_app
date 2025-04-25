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
        Schema::create('sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('customer_name');
            $table->string('phone')->nullable();
            $table->string('order_type');
            $table->string('table_number')->nullable();
            $table->decimal('subtotal');
            $table->decimal('discount')->default(0);
            $table->decimal('service_charge')->default(0);
            $table->decimal('tax');
            $table->decimal('total');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
