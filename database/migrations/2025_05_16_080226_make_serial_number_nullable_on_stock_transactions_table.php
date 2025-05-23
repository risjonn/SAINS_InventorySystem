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
        Schema::table('stock_transactions', function (Blueprint $table) {
            // Change serial_number to nullable
            $table->string('serial_number')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            // Revert to non-nullable
            $table->string('serial_number')->nullable(false)->change();
        });
    }
};
