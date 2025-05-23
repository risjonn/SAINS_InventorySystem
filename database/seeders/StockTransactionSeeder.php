<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stock;
use App\Models\StockTransaction;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Carbon\Carbon;

class StockTransactionSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('stock_transactions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $remarksOptions = [
            'Initial stock in',
            'Loaned to user',
            'Returned from loan',
            'Damaged',
            'Lost',
            'Maintenance',
            'Transfer between divisions',
            'Stock adjustment',
            'Replacement provided',
            'Testing',
            'Audit correction',
            'Warranty claim',
            'Received from vendor',
            'Sent for repair',
            'Disposed',
            'Reserved',
            'Used in project',
            'In transit',
            'Stock count correction',
            'Backordered'
        ];

        $userId = \App\Models\User::inRandomOrder()->first()->id; // pick one random user ID

        $stockTransactionCount = 100;

        for ($i = 1; $i <= $stockTransactionCount; $i++) {
            StockTransaction::create([
                'stock_id' => rand(1, 100),
                'type' => 'IN',
                'quantity' => rand(1, 5),
                'transaction_date' => now()->subDays(rand(0, 365)),
                'remarks' => $remarksOptions[array_rand($remarksOptions)],
                'handled_by' => $userId,
            ]);
        }
    }
}
