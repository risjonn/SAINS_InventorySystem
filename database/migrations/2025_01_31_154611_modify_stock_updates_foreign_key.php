<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('stock_updates', function (Blueprint $table) {
        $table->unsignedBigInteger('stock_id')->nullable()->change(); // Match stocks.id
        $table->foreign('stock_id')->references('id')->on('stocks')->onDelete('SET NULL');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('stock_updates', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['stock_id']);
            
            // Now, you can safely modify the column
            $table->bigInteger('stock_id')->nullable()->change();
        });
    }
    
};
