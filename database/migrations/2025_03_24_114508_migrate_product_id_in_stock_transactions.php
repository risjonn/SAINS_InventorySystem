<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class MigrateProductIdInStockTransactions extends Migration
{
    public function up()
    {
        // Retrieve all stock transaction records
        $transactions = DB::table('stock_transactions')->get();

        foreach ($transactions as $transaction) {
            // If product_code is not null, try to find a matching product
            if ($transaction->product_code) {
                $product = Product::where('product_code', $transaction->product_code)->first();
                if ($product) {
                    DB::table('stock_transactions')
                        ->where('id', $transaction->id)
                        ->update(['product_id' => $product->id]);
                }
            }
        }
    }

    public function down()
    {
        // Optionally, set product_id to null in all transactions
        DB::table('stock_transactions')->update(['product_id' => null]);
    }
}
