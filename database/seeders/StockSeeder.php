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

        // Expanded remark options for stocks
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
        $numStocks = 60; // Total stocks to generate

        // Get all products
        $products = Product::all();

        for ($i = 1; $i <= $numStocks; $i++) {
            // Randomly choose a product
            $product = $products->random();
            $remarks = $remarksOptions[array_rand($remarksOptions)];
            $divisionId = $divisions[array_rand($divisions)];
            
            // Create a unique serial number (e.g., SN001, SN002, etc.)
            $serialNumber = 'SN' . str_pad($i, 3, '0', STR_PAD_LEFT);

            Stock::create([
                'serial_number' => $serialNumber,
                'model'         => $product->model,
                'category'      => $product->category,
                'brand'         => $product->brand,
                'product_code'  => $product->product_code,
                'product_id'    => $product->id,
                'division_id'   => $divisionId,
                'remarks'       => $remarks,
                'status'        => 'available',
            ]);
        }
    }
}
