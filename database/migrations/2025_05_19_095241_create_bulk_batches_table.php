<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBulkBatchesTable extends Migration
{
    public function up()
    {
        Schema::create('bulk_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('division_id')->constrained()->cascadeOnDelete();
            $table->integer('initial_quantity');
            $table->integer('remaining_quantity');
            $table->timestamp('received_at')->nullable();
            $table->string('manual_requisition')->nullable();
            $table->string('collection_note')->nullable();
            $table->string('ticket_number')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->foreignId('bulk_batch_id')
                  ->nullable()
                  ->after('stock_id')
                  ->constrained('bulk_batches')
                  ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('bulk_batch_id');
        });
        Schema::dropIfExists('bulk_batches');
    }
};
