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
            $table->unsignedBigInteger('user_id');
            $table->string('category');  // We'll store the category name
            $table->unsignedTinyInteger('low_threshold')->default(10);      // Default: items below 10 are low
            $table->unsignedTinyInteger('critical_threshold')->default(3);  // Default: items below 3 are critical
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // Optionally add a unique index per user-category combination
            $table->unique(['user_id', 'category']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_product_thresholds');
    }
}