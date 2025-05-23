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
    Schema::table('bulk_batches', function (Blueprint $table) {
        $table->integer('loan_period')->default(90);
        $table->date('loan_start_date')->nullable();
    });
}

public function down()
{
    Schema::table('bulk_batches', function (Blueprint $table) {
        $table->dropColumn('loan_period');
        $table->dropColumn('loan_start_date');
    });
}


};
