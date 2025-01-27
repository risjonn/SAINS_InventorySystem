<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStocksTable extends Migration
{
    public function up()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->string('serial_number')->after('id');
            $table->string('brand')->after('serial_number');
            $table->string('model')->after('brand');
            $table->enum('category', ['Router', 'Switch', 'Modem', 'Access Points'])->after('model');
            $table->dropColumn('item_name');
            $table->dropColumn('item_type');
            $table->dropColumn('location');
        });
    }

    public function down()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropColumn(['serial_number', 'brand', 'model', 'category']);
            $table->string('item_name')->after('id');
            $table->string('item_type')->after('item_name');
            $table->string('location')->after('quantity')->nullable();
        });
    }
}