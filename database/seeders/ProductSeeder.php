<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // Clear the products table first
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('products')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Define products data for each category.
        // Adjust or add more as needed.
        $productsData = [
            'Router' => [
                ['brand' => 'Huawei', 'model' => 'MA5600T'],
                ['brand' => 'Cisco', 'model' => 'Cisco ISR 4000'],
            ],
            'Switch' => [
                ['brand' => 'Cisco', 'model' => 'Cisco Catalyst 2960'],
                ['brand' => 'HP', 'model' => 'HP ProCurve 5400'],
                ['brand' => 'Dell EMC', 'model' => 'Dell EMC Networking N1548'],
            ],
            'Modem' => [
                ['brand' => 'Netgear', 'model' => 'Nighthawk CM1200'],
                ['brand' => 'Arris', 'model' => 'Surfboard SB8200'],
            ],
            'Firewall' => [
                ['brand' => 'Fortinet', 'model' => 'FortiGate 60F'],
                ['brand' => 'Palo Alto', 'model' => 'PA-220'],
            ],
            'Server' => [
                ['brand' => 'Dell', 'model' => 'PowerEdge R740'],
                ['brand' => 'HP', 'model' => 'ProLiant DL380 Gen10'],
            ],
            'Laptop' => [
                ['brand' => 'Dell', 'model' => 'XPS 13'],
                ['brand' => 'Apple', 'model' => 'MacBook Pro 13'],
                ['brand' => 'Lenovo', 'model' => 'ThinkPad X1 Carbon'],
            ],
            'Printer' => [
                ['brand' => 'HP', 'model' => 'OfficeJet Pro 9015'],
                ['brand' => 'Canon', 'model' => 'PIXMA TS9120'],
            ],
            'Storage' => [
                ['brand' => 'Seagate', 'model' => 'Barracuda 4TB'],
                ['brand' => 'Western Digital', 'model' => 'WD Blue 4TB'],
            ],
            'Access Point' => [
                ['brand' => 'Ubiquiti', 'model' => 'UniFi AC Pro'],
                ['brand' => 'Cisco', 'model' => 'Aironet 2800'],
            ],
            'UPS' => [
                ['brand' => 'APC', 'model' => 'Back-UPS Pro 1500VA'],
                ['brand' => 'CyberPower', 'model' => 'CP1500PFCLCD'],
            ],
        ];

        // We'll generate a unique product code for each product. For example, use the first two letters of the category in uppercase plus a sequential number.
        foreach ($productsData as $category => $products) {
            $prefix = strtoupper(substr($category, 0, 2));
            $seq = 1;
            foreach ($products as $data) {
                $data['product_code'] = $prefix . str_pad($seq, 3, '0', STR_PAD_LEFT);
                $data['category'] = $category;
                $data['status'] = 'active';
                Product::create($data);
                $seq++;
            }
        }
    }
}
