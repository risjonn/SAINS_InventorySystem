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
        Schema::create('stock_updates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_id'); // Foreign key to stocks table
            $table->string('type'); // 'created', 'updated', 'deleted'
            $table->timestamps();

            // Foreign key constraint with ON DELETE CASCADE
            $table->foreign('stock_id')->references('id')->on('stocks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_updates');
    }
};
