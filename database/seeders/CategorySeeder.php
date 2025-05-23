<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run()
    {
        // Clear the categories table first
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $categories = [
            'Router',
            'Switch',
            'Firewall',
            'UPS',
            'PDU',
            'Access Point',
            'Server',
            'Firewall Appliance',
            'Modem',
            'Load Balancer',
            'Network Cable',
            'Storage Device',
            'Printer',
            'Laptop',
            'Desktop PC',
            'VoIP Phone',
            'Security Camera',
            'Projector',
            'NAS',
            'Battery Backup'
        ];

        foreach ($categories as $name) {
            Category::create(['name' => $name]);
        }
    }
}
