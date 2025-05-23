<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LiveDatabaseSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks to avoid constraint issues during truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate tables you want to clear in live
        DB::table('categories')->truncate();
        DB::table('products')->truncate();
        DB::table('stocks')->truncate();
        DB::table('stock_transactions')->truncate();
        DB::table('user_product_thresholds')->truncate();
        // Add more tables to truncate here if needed

        // Enable foreign key checks back
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Seed only divisions and users
        $this->call([
            DivisionSeeder::class,
            UserSeeder::class,
        ]);
    }
}
