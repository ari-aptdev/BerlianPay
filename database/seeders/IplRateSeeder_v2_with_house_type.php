<?php

namespace Database\Seeders;

use App\Models\IplRate;
use Illuminate\Database\Seeder;

class IplRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        IplRate::updateOrCreate(
            ['category' => 'rukem'],
            [
                'house_type' => 'rukem',
                'kas' => 10000,
                'sampah' => 30000,
                'kebersihan' => 40000,
                'rukem' => 5000,
                'total' => 85000,
            ]
        );

        IplRate::updateOrCreate(
            ['category' => 'non_rukem'],
            [
                'house_type' => 'non_rukem',
                'kas' => 10000,
                'sampah' => 30000,
                'kebersihan' => 40000,
                'rukem' => 0,
                'total' => 80000,
            ]
        );
    }
}
