<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Division;
use Illuminate\Support\Facades\DB;

class DivisionSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('divisions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $divisions = [
            ['name' => 'Kuching'],
            ['name' => 'Miri'],
            ['name' => 'Samarahan'],
            ['name' => 'Sri Aman'],
            ['name' => 'Bintulu'],
            ['name' => 'Kapit'],
        ];

        foreach ($divisions as $division) {
            Division::create($division);
        }
    }
}
