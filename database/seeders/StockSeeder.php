<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stock;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Division;

class StockSeeder extends Seeder
{
    public function run()
    {
        // Clear the stocks table first
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('stocks')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $remarksOptions = [
            'New stock',
            'Returned item',
            'Damaged packaging',
            'For loan',
            'Reserved',
            'Under maintenance',
            'Transferred',
            'Testing unit',
            'Obsolete',
            'Under warranty',
            'Loaned out',
            'In transit',
            'Replacement stock',
            'Checked and verified',
            'Lost',
            'Awaiting inspection',
            'Repair required',
            'In use',
            'Spare part',
            'Received from vendor'
        ];

        $products = Product::all();
        $divisions = Division::all();

        foreach ($products as $product) {
            foreach ($divisions as $division) {
                $quantity = rand(10, 40);

                for ($i = 0; $i < $quantity; $i++) {
                    Stock::create([
                        'product_id' => $product->id,
                        'serial_number' => strtoupper(bin2hex(random_bytes(5))),
                        'product_code' => $product->product_code,
                        'remarks' => $remarksOptions[array_rand($remarksOptions)],
                        'status' => 'available',
                        'division_id' => $division->id,
                    ]);
                }
            }
        }
    }
}
