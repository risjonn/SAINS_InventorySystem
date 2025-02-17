<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class StockSeeder extends Seeder
{
    public function run()
    {
        // Clear the stocks table first
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('stocks')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // A list of logical model names for network equipment
        $modelNames = [
            'Cisco Catalyst 2960',
            'Juniper MX480',
            'Huawei MA5600T',
            'Dell EMC Networking N1548',
            'Arista 7050X',
            'HP ProCurve 5400',
            'Extreme Networks Summit X450-G2',
            'Netgear ProSAFE M4300',
            'Brocade ICX 6610',
            'HPE FlexNetwork 5130'
        ];

        // Logical remark options
        $remarksOptions = [
            'Installed in main office',
            'Deployed in data center',
            'Located in branch office',
            'Ready for deployment',
            'Under routine maintenance',
            'Recently upgraded',
            'For backup operations'
        ];

        // Available categories for stocks
        $categories = ['Router', 'Switch', 'Modem'];

        $divisions = [1, 2, 3, 4, 5, 6];

        $numStocks = 30; // Total stocks to generate

        for ($i = 1; $i <= $numStocks; $i++) {
            $model = $modelNames[array_rand($modelNames)];
            $remarks = $remarksOptions[array_rand($remarksOptions)];
            $category = $categories[array_rand($categories)];
            $divisionId = $divisions[array_rand($divisions)];
            
            // Create a unique serial number (e.g. SN001, SN002, etc.)
            $serialNumber = 'SN' . str_pad($i, 3, '0', STR_PAD_LEFT);

            Stock::create([
                'serial_number' => $serialNumber,
                'model'         => $model,
                'category'      => $category,
                'division_id'   => $divisionId,
                'remarks'       => $remarks,
            ]);
        }
    }
}
