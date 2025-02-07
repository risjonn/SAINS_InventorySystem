<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockUpdatesTable extends Migration
{
    public function up()
    {
        Schema::create('stock_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_id')->constrained()->onDelete('cascade'); // Foreign key to the 'stocks' table
            $table->string('type');  // 'created', 'updated', 'deleted'
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_updates');
    }
}
