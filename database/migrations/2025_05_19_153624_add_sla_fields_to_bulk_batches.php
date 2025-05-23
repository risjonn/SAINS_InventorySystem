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
        $table->string('sla_category')->nullable()->after('collection_note');
        $table->string('sla')->nullable()->after('sla_category');
    });
}

public function down()
{
    Schema::table('bulk_batches', function (Blueprint $table) {
        $table->dropColumn(['sla_category', 'sla']);
    });
}
};
