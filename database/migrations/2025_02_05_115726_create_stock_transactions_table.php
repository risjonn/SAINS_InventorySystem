<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number');
            $table->enum('type', ['IN', 'OUT']);
            $table->unsignedBigInteger('handled_by'); // Make sure this is unsigned

            $table->text('remarks')->nullable();
            $table->timestamp('transaction_date')->useCurrent();

            $table->foreign('serial_number')->references('serial_number')->on('stocks')->onDelete('cascade');
            $table->foreign('handled_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_transactions');
    }
};
