<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // Clear the products table first
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('products')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $productsByCategory = [
            'Router' => [
                ['brand' => 'Huawei', 'model' => 'MA5600T', 'product_code' => 'HW-MA5600T'],
                ['brand' => 'Cisco', 'model' => 'ISR 4000', 'product_code' => 'CS-ISR4000'],
                ['brand' => 'Juniper', 'model' => 'MX480', 'product_code' => 'JP-MX480'],
                ['brand' => 'MikroTik', 'model' => 'CCR1009', 'product_code' => 'MK-CCR1009'],
                ['brand' => 'TP-Link', 'model' => 'Archer C7', 'product_code' => 'TP-ARCHC7'],
            ],
            'Switch' => [
                ['brand' => 'Cisco', 'model' => 'Catalyst 2960', 'product_code' => 'CS-C2960'],
                ['brand' => 'HP', 'model' => 'ProCurve 2810', 'product_code' => 'HP-P2810'],
                ['brand' => 'Netgear', 'model' => 'GS724T', 'product_code' => 'NG-GS724T'],
                ['brand' => 'D-Link', 'model' => 'DGS-1210', 'product_code' => 'DL-DGS1210'],
                ['brand' => 'Ubiquiti', 'model' => 'UniFi Switch 24', 'product_code' => 'UB-UNI24'],
            ],
            'Firewall' => [
                ['brand' => 'Fortinet', 'model' => 'FortiGate 60E', 'product_code' => 'FT-FG60E'],
                ['brand' => 'Palo Alto', 'model' => 'PA-220', 'product_code' => 'PA-220'],
                ['brand' => 'Cisco', 'model' => 'ASA 5506-X', 'product_code' => 'CS-ASA5506X'],
                ['brand' => 'SonicWall', 'model' => 'TZ400', 'product_code' => 'SW-TZ400'],
            ],
            'UPS' => [
                ['brand' => 'APC', 'model' => 'Smart-UPS 1500', 'product_code' => 'APC-SU1500'],
                ['brand' => 'Eaton', 'model' => '5P 1550', 'product_code' => 'ET-5P1550'],
                ['brand' => 'CyberPower', 'model' => 'CP1500PFCLCD', 'product_code' => 'CP-1500LCD'],
            ],
            'PDU' => [
                ['brand' => 'APC', 'model' => 'Metered PDU', 'product_code' => 'APC-MPDU'],
                ['brand' => 'CyberPower', 'model' => 'PDU81005', 'product_code' => 'CP-PDU81005'],
                ['brand' => 'Tripp Lite', 'model' => 'PDU1230', 'product_code' => 'TL-PDU1230'],
            ],
            'Access Point' => [
                ['brand' => 'Ubiquiti', 'model' => 'UniFi AP AC Lite', 'product_code' => 'UB-UNI-ACLITE'],
                ['brand' => 'Cisco', 'model' => 'Aironet 1832i', 'product_code' => 'CS-AIR1832I'],
                ['brand' => 'TP-Link', 'model' => 'EAP245', 'product_code' => 'TP-EAP245'],
            ],
            'Server' => [
                ['brand' => 'Dell', 'model' => 'PowerEdge R740', 'product_code' => 'DL-R740'],
                ['brand' => 'HP', 'model' => 'ProLiant DL380', 'product_code' => 'HP-DL380'],
                ['brand' => 'Lenovo', 'model' => 'ThinkSystem SR650', 'product_code' => 'LN-SR650'],
            ],
            'Firewall Appliance' => [
                ['brand' => 'Juniper', 'model' => 'SRX300', 'product_code' => 'JP-SRX300'],
                ['brand' => 'Fortinet', 'model' => 'FortiGate 100F', 'product_code' => 'FT-FG100F'],
                ['brand' => 'SonicWall', 'model' => 'NSA 4600', 'product_code' => 'SW-NSA4600'],
            ],
            'Modem' => [
                ['brand' => 'Netgear', 'model' => 'Nighthawk CM1200', 'product_code' => 'NG-CM1200'],
                ['brand' => 'Motorola', 'model' => 'MB7621', 'product_code' => 'MT-MB7621'],
                ['brand' => 'ARRIS', 'model' => 'SURFboard SB6183', 'product_code' => 'AR-SB6183'],
            ],
            'Load Balancer' => [
                ['brand' => 'F5 Networks', 'model' => 'BIG-IP iSeries', 'product_code' => 'F5-BIGIP'],
                ['brand' => 'Citrix', 'model' => 'NetScaler MPX', 'product_code' => 'CT-NETMPX'],
                ['brand' => 'A10 Networks', 'model' => 'Thunder ADC', 'product_code' => 'A10-THUNDER'],
            ],
            'Network Cable' => [
                ['brand' => 'Belden', 'model' => 'Cat6 UTP Cable', 'product_code' => 'BD-CAT6UTP'],
                ['brand' => 'Commscope', 'model' => 'Cat6 Cable', 'product_code' => 'CS-CAT6'],
                ['brand' => 'Panduit', 'model' => 'Cat6 Cable', 'product_code' => 'PD-CAT6'],
            ],
            'Storage Device' => [
                ['brand' => 'Seagate', 'model' => 'Backup Plus', 'product_code' => 'ST-BKPLUS'],
                ['brand' => 'Western Digital', 'model' => 'My Passport', 'product_code' => 'WD-MYPASS'],
                ['brand' => 'Synology', 'model' => 'DiskStation DS220+', 'product_code' => 'SY-DS220P'],
            ],
            'Printer' => [
                ['brand' => 'HP', 'model' => 'LaserJet Pro M404', 'product_code' => 'HP-LJM404'],
                ['brand' => 'Canon', 'model' => 'ImageCLASS MF743Cdw', 'product_code' => 'CN-MF743'],
                ['brand' => 'Epson', 'model' => 'WorkForce WF-2830', 'product_code' => 'EP-WF2830'],
            ],
            'Laptop' => [
                ['brand' => 'Dell', 'model' => 'Latitude 5420', 'product_code' => 'DL-LAT5420'],
                ['brand' => 'HP', 'model' => 'EliteBook 840', 'product_code' => 'HP-EB840'],
                ['brand' => 'Lenovo', 'model' => 'ThinkPad X1 Carbon', 'product_code' => 'LN-X1C'],
            ],
            'Desktop PC' => [
                ['brand' => 'Dell', 'model' => 'OptiPlex 7080', 'product_code' => 'DL-OPT7080'],
                ['brand' => 'HP', 'model' => 'ProDesk 600', 'product_code' => 'HP-PD600'],
                ['brand' => 'Lenovo', 'model' => 'ThinkCentre M720', 'product_code' => 'LN-M720'],
            ],
            'VoIP Phone' => [
                ['brand' => 'Cisco', 'model' => 'IP Phone 8845', 'product_code' => 'CS-IP8845'],
                ['brand' => 'Polycom', 'model' => 'VVX 601', 'product_code' => 'PL-VVX601'],
                ['brand' => 'Yealink', 'model' => 'T46S', 'product_code' => 'YL-T46S'],
            ],
            'Security Camera' => [
                ['brand' => 'Hikvision', 'model' => 'DS-2CD2043G0-I', 'product_code' => 'HK-DS2043'],
                ['brand' => 'Dahua', 'model' => 'IPC-HFW4431R-Z', 'product_code' => 'DH-HFW4431'],
                ['brand' => 'Axis', 'model' => 'P5635-E', 'product_code' => 'AX-P5635'],
            ],
            'Projector' => [
                ['brand' => 'Epson', 'model' => 'PowerLite 1781W', 'product_code' => 'EP-PL1781'],
                ['brand' => 'BenQ', 'model' => 'MH535FHD', 'product_code' => 'BQ-MH535'],
                ['brand' => 'Optoma', 'model' => 'HD143X', 'product_code' => 'OP-HD143X'],
            ],
            'NAS' => [
                ['brand' => 'Synology', 'model' => 'DS920+', 'product_code' => 'SY-DS920'],
                ['brand' => 'QNAP', 'model' => 'TS-451+', 'product_code' => 'QN-TS451'],
                ['brand' => 'Western Digital', 'model' => 'My Cloud EX2 Ultra', 'product_code' => 'WD-MCEX2'],
            ],
            'Battery Backup' => [
                ['brand' => 'APC', 'model' => 'Back-UPS Pro 1500', 'product_code' => 'APC-BUPS1500'],
                ['brand' => 'CyberPower', 'model' => 'CP1500AVRLCD', 'product_code' => 'CP-CP1500'],
                ['brand' => 'Eaton', 'model' => '9PX UPS', 'product_code' => 'ET-9PX'],
            ],
        ];

        foreach ($productsByCategory as $categoryName => $products) {
            $category = Category::where('name', $categoryName)->first();
            if (!$category) continue;

            foreach ($products as $product) {
                Product::create([
                    'category' => $category->name,
                    'brand' => $product['brand'],
                    'model' => $product['model'],
                    'product_code' => $product['product_code'],
                ]);
            }
        }
    }
}
