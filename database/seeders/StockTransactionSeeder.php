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
        // A list of remark phrases for transactions
        $remarksOptions = [
            'Initial stock received',
            'New shipment arrived',
            'Delivered to branch office',
            'Routine restock completed',
            'Emergency purchase executed'
        ];

        // Get all stocks
        $stocks = Stock::all();

        foreach ($stocks as $stock) {
            // Find a random admin in the stock's division
            $admin = User::where('division_id', $stock->division_id)
                         ->where('role', 'Admin')
                         ->inRandomOrder()
                         ->first();

            // If no admin is found in that division, fallback to any admin (ignoring the user manager)
            if (!$admin) {
                $admin = User::where('role', 'Admin')->first();
            }

            $remarks = $remarksOptions[array_rand($remarksOptions)] . " - " . $stock->model;

            StockTransaction::create([
                'serial_number'    => $stock->serial_number,
                'stock_model'      => $stock->model,
                'category'         => $stock->category,
                'type'             => 'IN',
                'handled_by'       => $admin ? $admin->id : null,
                'remarks'          => $remarks,
                'transaction_date' => Carbon::now()->subDays(rand(1, 30)),
            ]);
        }
    }
}
