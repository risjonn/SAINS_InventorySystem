<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1) Drop any old single-column unique indexes (ignore if they don't exist)
        try {
            DB::statement('ALTER TABLE `stocks` DROP INDEX `stocks_serial_number_unique`');
        } catch (\Exception $e) {
            // ignore
        }
        try {
            DB::statement('ALTER TABLE `stocks` DROP INDEX `serial_number_unique`');
        } catch (\Exception $e) {
            // ignore
        }

        // 2) Create composite unique on (serial_number, product_code) if not already there
        try {
            DB::statement(
                'ALTER TABLE `stocks` 
                 ADD UNIQUE `stocks_serial_product_unique` (`serial_number`,`product_code`)'
            );
        } catch (\Exception $e) {
            // If index already exists, ignore
        }
    }

    public function down()
    {
        // 1) Drop our composite index (ignore if not there)
        try {
            Schema::table('stocks', function (Blueprint $table) {
                $table->dropUnique('stocks_serial_product_unique');
            });
        } catch (\Exception $e) {
            // ignore
        }

        // 2) Restore single-column unique on serial_number
        Schema::table('stocks', function (Blueprint $table) {
            $table->unique('serial_number', 'serial_number_unique');
        });
    }
};
