<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Insert sample divisions
        DB::table('divisions')->insert([
            ['name' => 'Kuching', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sri Aman', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Betong', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Insert sample users
        DB::table('users')->insert([
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'Admin',
                'division_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'User One',
                'email' => 'user1@example.com',
                'password' => Hash::make('password'),
                'role' => 'User',
                'division_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Insert sample stocks
        DB::table('stocks')->insert([
            [
                'serial_number' => 'SN123456',
                'model' => 'Huawei MA5608T',
                'category' => 'Router',
                'division_id' => 1,
                'remarks' => 'Brand new',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'serial_number' => 'SN654321',
                'model' => 'Cisco C9200',
                'category' => 'Switch',
                'division_id' => 2,
                'remarks' => 'Slightly used',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Insert sample stock transactions (IN/OUT)
        DB::table('stock_transactions')->insert([
            [
                'serial_number' => 'SN123456',
                'type' => 'IN',
                'handled_by' => 1, // Admin User
                'remarks' => 'Added to inventory',
                'transaction_date' => Carbon::now()->subDays(3),
            ],
            [
                'serial_number' => 'SN654321',
                'type' => 'IN',
                'handled_by' => 2, // User One
                'remarks' => 'Received from supplier',
                'transaction_date' => Carbon::now()->subDays(2),
            ],
            [
                'serial_number' => 'SN123456',
                'type' => 'OUT',
                'handled_by' => 1, // Admin User
                'remarks' => 'Deployed to site',
                'transaction_date' => Carbon::now()->subDay(),
            ],
        ]);
    }
}
