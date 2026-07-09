<?php

namespace Database\Seeders;

use App\Models\House;
use Illuminate\Database\Seeder;

class HouseSeeder extends Seeder
{
    public function run(): void
    {
        $houses = [
            ['block' => 'A', 'house_number' => '01', 'owner_name' => 'Rudi Hartono', 'phone' => '081211110001', 'nik' => '3171011203900001'],
            ['block' => 'A', 'house_number' => '02', 'owner_name' => 'Wahyu Nugroho', 'phone' => '081211110002', 'nik' => '3172012207880002'],
            ['block' => 'A', 'house_number' => '04', 'owner_name' => 'Siti Rahma', 'phone' => '081234000002', 'nik' => '3171030905980001'],
            ['block' => 'B', 'house_number' => '03', 'owner_name' => 'Hendra Gunawan', 'phone' => '081211110004', 'nik' => '3275011508850004'],
            ['block' => 'B', 'house_number' => '07', 'owner_name' => 'Budi Santoso', 'phone' => '081234000003', 'nik' => '3216012811920003'],
            ['block' => 'C', 'house_number' => '05', 'owner_name' => 'Fajar Ramadhan', 'phone' => '081211110006', 'nik' => '3173010106910006'],
            ['block' => 'C', 'house_number' => '12', 'owner_name' => 'Andi Wijaya', 'phone' => '081234000001', 'nik' => '3175011704890007'],
            ['block' => 'D', 'house_number' => '02', 'owner_name' => 'Dewi Lestari', 'phone' => '081234000004', 'nik' => '3275014209870008'],
            ['block' => 'D', 'house_number' => '09', 'owner_name' => 'Eko Prasetyo', 'phone' => '081234000005', 'nik' => '3216012505890009'],
            ['block' => 'E', 'house_number' => '01', 'owner_name' => 'Gina Melati', 'phone' => '081211110009', 'nik' => '3174014811930010'],
        ];

        foreach ($houses as $house) {
            House::create($house);
        }
    }
}
