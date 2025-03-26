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
            ['name' => 'Router', 'active' => true],
            ['name' => 'Switch', 'active' => true],
            ['name' => 'Modem', 'active' => true],
            ['name' => 'Firewall', 'active' => true],
            ['name' => 'Server', 'active' => true],
            ['name' => 'Laptop', 'active' => true],
            ['name' => 'Printer', 'active' => true],
            ['name' => 'Storage', 'active' => true],
            ['name' => 'Access Point', 'active' => true],
            ['name' => 'UPS', 'active' => true],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }
    }
}
