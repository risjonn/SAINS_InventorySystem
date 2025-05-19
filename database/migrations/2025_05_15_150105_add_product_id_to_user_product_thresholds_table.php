<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::table('user_product_thresholds', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')
                  ->nullable()
                  ->after('category');
        });
    }

    public function down()
    {
        Schema::table('user_product_thresholds', function (Blueprint $table) {
            $table->dropColumn('product_id');
        });
    }
};
