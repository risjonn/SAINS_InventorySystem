<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixStocksSerialNumberUniqueIndex extends Migration
{
    public function up()
    {
        Schema::table('stocks', function (Blueprint $table) {
            // Drop the existing unique index on serial_number
            $table->dropUnique('stocks_serial_number_unique'); // this is usually the name; if not, replace with your actual index name

            // Create a composite unique index on serial_number + product_code
            $table->unique(['serial_number', 'product_code'], 'stocks_serial_product_unique');
        });
    }

    public function down()
    {
        Schema::table('stocks', function (Blueprint $table) {
            // Drop the composite unique index
            $table->dropUnique('stocks_serial_product_unique');

            // Restore the unique index on serial_number only
            $table->unique('serial_number');
        });
    }
};
