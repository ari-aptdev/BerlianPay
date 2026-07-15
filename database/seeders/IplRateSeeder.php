<?php

namespace Database\Seeders;

use App\Models\IplRate;
use Illuminate\Database\Seeder;

class IplRateSeeder extends Seeder
{
    public function run(): void
    {
        $rates = [
            ['house_type' => 'Tipe 36', 'nominal' => 150000, 'effective_date' => '2025-01-01'],
            ['house_type' => 'Tipe 45', 'nominal' => 200000, 'effective_date' => '2025-01-01'],
            ['house_type' => 'Tipe 54', 'nominal' => 250000, 'effective_date' => '2025-01-01'],
        ];

        foreach ($rates as $rate) {
            IplRate::create($rate);
        }
    }
}
