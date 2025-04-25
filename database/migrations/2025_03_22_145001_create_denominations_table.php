<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('denominations', function (Blueprint $table) {
            $table->id(); // Auto-incremental ID column
            $table->decimal('value', 8, 2); // Value of the denomination (e.g., 1000, 500, 200, etc.)
            $table->integer('quantity');    // Quantity of bills/coins of this denomination
            $table->timestamps();           // created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('denominations'); // Drop the table if the migration is rolled back
    }
};
