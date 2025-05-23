<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class UserProductThresholdSeeder extends Seeder
{
    public function run()
    {
        // Disable FK checks and truncate the thresholds table
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('user_product_thresholds')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $users = User::all();
        $products = Product::all(); // No category relation, category stored as string

        foreach ($users as $user) {
            foreach ($products as $product) {
                DB::table('user_product_thresholds')->insert([
                    'user_id' => $user->id,
                    'category' => $product->category, // use category string column
                    'product_id' => $product->id,
                    'low_threshold' => 30,
                    'critical_threshold' => 20,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
