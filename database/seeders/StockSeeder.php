<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stock;

class StockSeeder extends Seeder
{
    public function run()
    {
        Stock::insert([
            [
                'serial_number' => 'SN12345678',
                'brand' => 'Cisco',
                'model' => 'RV340',
                'category' => 'Router',
                'quantity' => 15,
                'updated_at' => now(),
                'created_at' => now(),
            ],
            [
                'serial_number' => 'SN87654321',
                'brand' => 'TP-Link',
                'model' => 'Archer AX6000',
                'category' => 'Access Points',
                'quantity' => 20,
                'updated_at' => now(),
                'created_at' => now(),
            ],
            [
                'serial_number' => 'SN11223344',
                'brand' => 'Netgear',
                'model' => 'Nighthawk RAX',
                'category' => 'Router',
                'quantity' => 10,
                'updated_at' => now(),
                'created_at' => now(),
            ],
            [
                'serial_number' => 'SN33445566',
                'brand' => 'Huawei',
                'model' => 'MA5608T',
                'category' => 'Modem',
                'quantity' => 25,
                'updated_at' => now(),
                'created_at' => now(),
            ],
            [
                'serial_number' => 'SN55667788',
                'brand' => 'Juniper',
                'model' => 'EX3400',
                'category' => 'Switch',
                'quantity' => 18,
                'updated_at' => now(),
                'created_at' => now(),
            ],
            [
                'serial_number' => 'SN98765432',
                'brand' => 'MikroTik',
                'model' => 'RB3011',
                'category' => 'Router',
                'quantity' => 12,
                'updated_at' => now(),
                'created_at' => now(),
            ],
            [
                'serial_number' => 'SN99887766',
                'brand' => 'Ubiquiti',
                'model' => 'UniFi AP-AC',
                'category' => 'Access Points',
                'quantity' => 30,
                'updated_at' => now(),
                'created_at' => now(),
            ],
            [
                'serial_number' => 'SN44556677',
                'brand' => 'Zyxel',
                'model' => 'C3000Z',
                'category' => 'Modem',
                'quantity' => 16,
                'updated_at' => now(),
                'created_at' => now(),
            ],
            [
                'serial_number' => 'SN22334455',
                'brand' => 'Cisco',
                'model' => 'C9300',
                'category' => 'Switch',
                'quantity' => 8,
                'updated_at' => now(),
                'created_at' => now(),
            ],
            [
                'serial_number' => 'SN66778899',
                'brand' => 'D-Link',
                'model' => 'DIR-882',
                'category' => 'Router',
                'quantity' => 14,
                'updated_at' => now(),
                'created_at' => now(),
            ],
            [
                'serial_number' => 'SN11235813',
                'brand' => 'ASUS',
                'model' => 'RT-AX88U',
                'category' => 'Router',
                'quantity' => 6,
                'updated_at' => now(),
                'created_at' => now(),
            ],
            [
                'serial_number' => 'SN19283746',
                'brand' => 'Cisco',
                'model' => 'ISR4321',
                'category' => 'Router',
                'quantity' => 9,
                'updated_at' => now(),
                'created_at' => now(),
            ],
            [
                'serial_number' => 'SN54637281',
                'brand' => 'Netgear',
                'model' => 'Orbi RBK50',
                'category' => 'Access Points',
                'quantity' => 22,
                'updated_at' => now(),
                'created_at' => now(),
            ],
            [
                'serial_number' => 'SN65984732',
                'brand' => 'Juniper',
                'model' => 'QFX5100',
                'category' => 'Switch',
                'quantity' => 5,
                'updated_at' => now(),
                'created_at' => now(),
            ],
            [
                'serial_number' => 'SN35467892',
                'brand' => 'Huawei',
                'model' => 'HG8245H',
                'category' => 'Modem',
                'quantity' => 17,
                'updated_at' => now(),
                'created_at' => now(),
            ],
        ]);
    }
}
