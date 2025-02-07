<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StockTransaction;
use App\Models\Stock;

class StockTransactionSeeder extends Seeder
{
    public function run()
    {
        $transactions = StockTransaction::all();
        foreach ($transactions as $transaction) {
            $stock = Stock::where('serial_number', $transaction->serial_number)->first();
            if($stock) {
                $transaction->update([
                    'stock_model' => $stock->model
                ]);
            }
        }
    }
}
