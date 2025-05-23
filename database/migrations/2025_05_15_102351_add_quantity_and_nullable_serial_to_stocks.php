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
    Schema::table('stocks', function (Blueprint $table) {
        // allow null serials
        $table->string('serial_number')->nullable()->change();
        // track bulk qty
        $table->unsignedInteger('quantity')->default(1);
    });
}
public function down()
{
    Schema::table('stocks', function (Blueprint $table) {
        $table->string('serial_number')->nullable(false)->change();
        $table->dropColumn('quantity');
    });
}

};
