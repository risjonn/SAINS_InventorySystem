<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create a Super Admin
        User::create([
            'username'    => 'superadmin',
            'name'        => 'Super Admin',
            'email'       => 'superadmin@example.com',
            'password'    => Hash::make('password'),
            'division_id' => 1,
            'role'        => 'Super Admin',
        ]);

        // Create Admins (one per division for example)
        $admins = [
            [
                'username'    => 'admin_kuching',
                'name'        => 'Admin Kuching',
                'email'       => 'admin_kuching@example.com',
                'password'    => Hash::make('password'),
                'division_id' => 1, // Kuching
                'role'        => 'Admin',
            ],
            [
                'username'    => 'admin_miri',
                'name'        => 'Admin Miri',
                'email'       => 'admin_miri@example.com',
                'password'    => Hash::make('password'),
                'division_id' => 2, // Miri
                'role'        => 'Admin',
            ],
            [
                'username'    => 'admin_samarahan',
                'name'        => 'Admin Samarahan',
                'email'       => 'admin_samarahan@example.com',
                'password'    => Hash::make('password'),
                'division_id' => 3, // Samarahan
                'role'        => 'Admin',
            ],
            [
                'username'    => 'admin_sriaman',
                'name'        => 'Admin Sri Aman',
                'email'       => 'admin_sriaman@example.com',
                'password'    => Hash::make('password'),
                'division_id' => 4, // Sri Aman
                'role'        => 'Admin',
            ],
            [
                'username'    => 'admin_bintulu',
                'name'        => 'Admin Bintulu',
                'email'       => 'admin_bintulu@example.com',
                'password'    => Hash::make('password'),
                'division_id' => 5, // Bintulu
                'role'        => 'Admin',
            ],
            [
                'username'    => 'admin_kapit',
                'name'        => 'Admin Kapit',
                'email'       => 'admin_kapit@example.com',
                'password'    => Hash::make('password'),
                'division_id' => 6, // Kapit
                'role'        => 'Admin',
            ],
        ];

        foreach ($admins as $admin) {
            User::create($admin);
        }
    }
}
