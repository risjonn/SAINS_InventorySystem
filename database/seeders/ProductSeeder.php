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

        // Sample products with additional product codes
        $products = [
            [
                'product_code' => 'RO001',
                'category'     => 'Router',
                'brand'        => 'Huawei',
                'model'        => 'MA5600T',
                'status'       => 'active'
            ],
            [
                'product_code' => 'SW001',
                'category'     => 'Switch',
                'brand'        => 'Cisco',
                'model'        => 'Cisco Catalyst 2960',
                'status'       => 'active'
            ],
            [
                'product_code' => 'MD001',
                'category'     => 'Modem',
                'brand'        => 'Netgear',
                'model'        => 'Netgear Nighthawk',
                'status'       => 'active'
            ],
            [
                'product_code' => 'RO002',
                'category'     => 'Router',
                'brand'        => 'Juniper',
                'model'        => 'Juniper MX480',
                'status'       => 'inactive'
            ],
            [
                'product_code' => 'SW002',
                'category'     => 'Switch',
                'brand'        => 'Dell EMC',
                'model'        => 'Dell EMC Networking N1548',
                'status'       => 'active'
            ],
            [
                'product_code' => 'AR001',
                'category'     => 'Router',
                'brand'        => 'Arista',
                'model'        => 'Arista 7050X',
                'status'       => 'active'
            ],
            [
                'product_code' => 'HP001',
                'category'     => 'Switch',
                'brand'        => 'HP ProCurve',
                'model'        => 'HP ProCurve 5400',
                'status'       => 'active'
            ],
            [
                'product_code' => 'EX001',
                'category'     => 'Switch',
                'brand'        => 'Extreme Networks',
                'model'        => 'Extreme Networks Summit X450-G2',
                'status'       => 'active'
            ],
            [
                'product_code' => 'NG001',
                'category'     => 'Switch',
                'brand'        => 'Netgear',
                'model'        => 'Netgear ProSAFE M4300',
                'status'       => 'inactive'
            ],
            [
                'product_code' => 'BR001',
                'category'     => 'Router',
                'brand'        => 'Brocade',
                'model'        => 'Brocade ICX 6610',
                'status'       => 'active'
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }
    }
}
