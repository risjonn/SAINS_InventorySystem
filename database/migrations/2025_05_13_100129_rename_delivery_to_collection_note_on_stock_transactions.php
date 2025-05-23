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
        // ← change 'delivery' → 'delivery_note'
        $table->renameColumn('delivery_note', 'collection_note');
    });
}

public function down()
{
    Schema::table('stock_transactions', function (Blueprint $table) {
        $table->renameColumn('collection_note', 'delivery_note');
    });
}

};
