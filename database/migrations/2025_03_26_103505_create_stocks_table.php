<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStocksTable extends Migration
{
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->unique();
            $table->string('product_code')->nullable();
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
            $table->string('requisition_no')->nullable();
            $table->foreignId('division_id')->constrained('divisions')->onDelete('cascade');
            $table->text('remarks')->nullable();
            $table->enum('status', ['available','IN', 'OUT', 'LOAN', 'RETURN'])->default('available');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stocks');
    }
}
