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
        $table->integer('quantity')->nullable()->after('type');
    });
}

public function down()
{
    Schema::table('stock_transactions', function (Blueprint $table) {
        $table->dropColumn('quantity');
    });
}

};
