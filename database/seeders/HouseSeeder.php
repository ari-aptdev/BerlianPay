<?php

namespace Database\Seeders;

use App\Models\House;
use Illuminate\Database\Seeder;

class HouseSeeder extends Seeder
{
    public function run(): void
    {
        $houses = [
            ['block' => 'A', 'house_number' => '01', 'owner_name' => 'Rudi Hartono', 'phone' => '081211110001', 'type' => 'Tipe 36'],
            ['block' => 'A', 'house_number' => '02', 'owner_name' => 'Wahyu Nugroho', 'phone' => '081211110002', 'type' => 'Tipe 36'],
            ['block' => 'A', 'house_number' => '04', 'owner_name' => 'Siti Rahma', 'phone' => '081234000002', 'type' => 'Tipe 45'],
            ['block' => 'B', 'house_number' => '03', 'owner_name' => 'Hendra Gunawan', 'phone' => '081211110004', 'type' => 'Tipe 45'],
            ['block' => 'B', 'house_number' => '07', 'owner_name' => 'Budi Santoso', 'phone' => '081234000003', 'type' => 'Tipe 45'],
            ['block' => 'C', 'house_number' => '05', 'owner_name' => 'Fajar Ramadhan', 'phone' => '081211110006', 'type' => 'Tipe 54'],
            ['block' => 'C', 'house_number' => '12', 'owner_name' => 'Andi Wijaya', 'phone' => '081234000001', 'type' => 'Tipe 54'],
            ['block' => 'D', 'house_number' => '02', 'owner_name' => 'Dewi Lestari', 'phone' => '081234000004', 'type' => 'Tipe 36'],
            ['block' => 'D', 'house_number' => '09', 'owner_name' => 'Eko Prasetyo', 'phone' => '081234000005', 'type' => 'Tipe 45'],
            ['block' => 'E', 'house_number' => '01', 'owner_name' => 'Gina Melati', 'phone' => '081211110009', 'type' => 'Tipe 36'],
        ];

        foreach ($houses as $house) {
            House::create($house);
        }
    }
}
