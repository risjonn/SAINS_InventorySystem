<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            DivisionSeeder::class,
            UserSeeder::class,
        ]);

        if (app()->environment('dummy')) {
            $this->call([
                CategorySeeder::class,
                ProductSeeder::class,
                StockSeeder::class,
                StockTransactionSeeder::class,
                UserProductThresholdSeeder::class,
            ]);
        }
    }
}
