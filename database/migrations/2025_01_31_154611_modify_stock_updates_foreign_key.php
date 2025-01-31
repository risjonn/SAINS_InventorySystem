<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('stock_updates', function (Blueprint $table) {
            $table->dropForeign(['stock_id']); // Drop existing foreign key
            $table->foreign('stock_id')->references('id')->on('stocks')->nullOnDelete(); // Allow NULL when stock is deleted
        });
    }

    public function down()
    {
        Schema::table('stock_updates', function (Blueprint $table) {
            $table->dropForeign(['stock_id']); // Remove the modified foreign key
            $table->foreign('stock_id')->references('id')->on('stocks')->onDelete('cascade'); // Revert back if needed
        });
    }
};
