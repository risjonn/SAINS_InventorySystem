<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stock;
use App\Models\StockTransaction;
use App\Models\User;
use Carbon\Carbon;

class StockTransactionSeeder extends Seeder
{
    public function run()
    {
        $remarksOptions = [
            'Initial stock received',
            'New shipment arrived',
            'Delivered to branch office',
            'Routine restock completed',
            'Emergency purchase executed',
            'Stock adjustment after audit',
            'Manual entry for low stock',
            'Replenishment confirmed',
            'Returned from maintenance',
            'Discrepancy resolved'
        ];

        $stocks = Stock::all();

        foreach ($stocks as $stock) {
            // Find a random admin in the stock's division.
            $admin = \App\Models\User::where('division_id', $stock->division_id)
                         ->where('role', 'Admin')
                         ->inRandomOrder()
                         ->first();

            // Fallback to any admin if not found.
            if (!$admin) {
                $admin = \App\Models\User::where('role', 'Admin')->first();
            }

            $remarks = $remarksOptions[array_rand($remarksOptions)] . " - " . $stock->product->model;

            StockTransaction::create([
                'serial_number'    => $stock->serial_number,
                'type'             => 'IN',
                'handled_by'       => $admin ? $admin->id : null,
                'remarks'          => $remarks,
                'transaction_date' => Carbon::now()->subDays(rand(1, 30)),
                'product_id'       => $stock->product_id,
                'product_code'     => $stock->product_code,
                // Optionally, if you need to store category or brand:
                // 'category'         => $stock->product ? $stock->product->category : null,
                // 'brand'            => $stock->product ? $stock->product->brand : null,
                // 'stock_model'      => $stock->product ? $stock->product->model : $stock->model,
            ]);
        }
    }
}
