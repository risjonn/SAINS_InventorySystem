<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductIdToStockTransactionsTable extends Migration
{
    public function up()
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            // Add a new column 'product_id' as an unsigned big integer, nullable for now.
            $table->unsignedBigInteger('product_id')->nullable()->after('product_code');

            // Add a foreign key constraint linking to the products table.
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            // First drop the foreign key, then the column.
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
        });
    }
}