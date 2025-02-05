<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->unique();
            $table->string('model'); // Includes brand + model name
            $table->enum('category', ['Router', 'Switch', 'Modem']);
            $table->foreignId('division_id')->constrained('divisions')->onDelete('cascade');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('stocks');
    }
};
