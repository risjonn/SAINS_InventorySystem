<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number');
            $table->string('product_code')->nullable();
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
            $table->string('requisition_no')->nullable();
            $table->enum('type', ['IN', 'OUT', 'LOAN', 'RETURN'])->default('IN');
            $table->foreignId('handled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('remarks')->nullable();
            $table->timestamp('transaction_date')->useCurrent();
            // No updated_at is necessary, but you can include timestamps() if needed.
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_transactions');
    }
}
