<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserProductThresholdsTable extends Migration
{
    public function up()
    {
        Schema::create('user_product_thresholds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('category');
            $table->integer('low_threshold');
            $table->integer('critical_threshold');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_product_thresholds');
    }
}
