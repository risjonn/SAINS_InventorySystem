<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLoanFieldsToStockTransactions extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_transactions', 'loan_start_date')) {
                $table->date('loan_start_date')->nullable();
            }
            if (!Schema::hasColumn('stock_transactions', 'due_date')) {
                $table->date('due_date')->nullable();
            }
            if (!Schema::hasColumn('stock_transactions', 'loan_period')) {
                $table->unsignedInteger('loan_period')->nullable();
            }
            if (!Schema::hasColumn('stock_transactions', 'delivery_note')) {
                $table->string('delivery_note')->nullable();
            }
            if (!Schema::hasColumn('stock_transactions', 'document_number')) {
                $table->string('document_number')->nullable();
            }
            if (!Schema::hasColumn('stock_transactions', 'name')) {
                $table->string('name')->nullable();
            }
            if (!Schema::hasColumn('stock_transactions', 'taken_by')) {
                $table->string('taken_by')->nullable();
            }
            if (!Schema::hasColumn('stock_transactions', 'ticket_number')) {
                $table->string('ticket_number')->nullable();
            }
            if (!Schema::hasColumn('stock_transactions', 'manual_requisition')) {
                $table->string('manual_requisition')->nullable();
            }
            if (!Schema::hasColumn('stock_transactions', 'manual_date')) {
                $table->date('manual_date')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->dropColumn([
                'loan_start_date',
                'due_date',
                'loan_period',
                'delivery_note',
                'document_number',
                'name',
                'taken_by',
                'ticket_number',
                'manual_requisition',
                'manual_date',
            ]);
        });
    }
}
