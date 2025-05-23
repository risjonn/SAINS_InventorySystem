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
        $table->unsignedBigInteger('stock_id')
              ->nullable()
              ->after('id');

        $table->foreign('stock_id')
              ->references('id')
              ->on('stocks')
              ->onDelete('set null');
    });
}

public function down()
{
    Schema::table('stock_transactions', function (Blueprint $table) {
        $table->dropForeign(['stock_id']);
        $table->dropColumn('stock_id');
    });
}

};
