<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stock;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class StockSeeder extends Seeder
{
    public function run()
    {
        // Clear the stocks table first
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('stocks')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $remarksOptions = [
            'Installed in main office',
            'Deployed in data center',
            'Located in branch office',
            'Ready for deployment',
            'Under routine maintenance',
            'Recently upgraded',
            'For backup operations',
            'Pending inspection',
            'Requires firmware update',
            'Under performance testing'
        ];

        $divisions = [1, 2, 3, 4, 5, 6];

        // Generate a random number of stocks between 60 and 100.
        $numStocks = rand(60, 100);

        // Get all products from the database.
        $products = Product::all();

        for ($i = 1; $i <= $numStocks; $i++) {
            $product = $products->random();
            $remarks = $remarksOptions[array_rand($remarksOptions)];
            $divisionId = $divisions[array_rand($divisions)];

            // Create a unique serial number (e.g. SN001, SN002, etc.)
            $serialNumber = 'SN' . str_pad($i, 3, '0', STR_PAD_LEFT);

            Stock::create([
                'serial_number' => $serialNumber,
                'division_id'   => $divisionId,
                'remarks'       => $remarks,
                'status'        => 'available',
                'product_id'    => $product->id,
                // Optionally, you can also store product_code from the product:
                'product_code'  => $product->product_code,
            ]);
        }
    }
}
