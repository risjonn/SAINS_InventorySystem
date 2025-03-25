<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class MigrateProductIdInStocks extends Migration
{
    public function up()
    {
        // Retrieve all stock records
        $stocks = DB::table('stocks')->get();

        foreach ($stocks as $stock) {
            // If product_code is not null, try to find a matching product
            if ($stock->product_code) {
                $product = Product::where('product_code', $stock->product_code)->first();
                if ($product) {
                    DB::table('stocks')
                        ->where('id', $stock->id)
                        ->update(['product_id' => $product->id]);
                }
            }
        }
    }

    public function down()
    {
        // Optionally, set product_id back to null for all stocks
        DB::table('stocks')->update(['product_id' => null]);
    }
}