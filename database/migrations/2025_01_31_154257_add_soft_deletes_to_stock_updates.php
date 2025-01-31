<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('stock_updates', function (Blueprint $table) {
            $table->softDeletes(); // Add soft deletes column
        });
    }

    public function down()
    {
        Schema::table('stock_updates', function (Blueprint $table) {
            $table->dropSoftDeletes(); // Remove soft deletes column if rollback
        });
    }
};
