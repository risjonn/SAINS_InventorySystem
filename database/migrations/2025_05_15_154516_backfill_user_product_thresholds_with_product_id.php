<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class BackfillUserProductThresholdsWithProductId extends Migration
{
    public function up()
    {
        // 1) Read each existing category-based threshold
        $thresholds = DB::table('user_product_thresholds')
            ->whereNull('product_id')
            ->get();

        foreach ($thresholds as $thr) {
            // 2) Find all products with that category name
            $products = DB::table('products')
                ->where('category', $thr->category)
                ->get();

            // 3) Insert one threshold row per product
            foreach ($products as $prod) {
                DB::table('user_product_thresholds')->insert([
                    'user_id'            => $thr->user_id,
                    'category'           => $thr->category,
                    'product_id'         => $prod->id,
                    'low_threshold'      => 30,
                    'critical_threshold' => 20,
                    'created_at'         => $thr->created_at,
                    'updated_at'         => $thr->updated_at,
                ]);
            }
        }

        // 4) Remove the old category-based rows
        DB::table('user_product_thresholds')
            ->whereNull('product_id')
            ->delete();
    }

    public function down()
    {
        //
    }
}