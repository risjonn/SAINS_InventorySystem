<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Division;

class DivisionSeeder extends Seeder
{
    public function run()
    {
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
