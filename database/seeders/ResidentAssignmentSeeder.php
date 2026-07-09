<?php

namespace Database\Seeders;

use App\Models\House;
use App\Models\User;
use Illuminate\Database\Seeder;

class ResidentAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        // Pasangkan warga demo ke rumah yang namanya cocok (mempermudah demo ke client)
        $pairs = [
            'andi@warga.test' => 'C12',
            'siti@warga.test' => 'A04',
            'budi@warga.test' => 'B07',
            'dewi@warga.test' => 'D02',
            'eko@warga.test' => 'D09',
        ];

        foreach ($pairs as $email => $blockHouse) {
            $block = substr($blockHouse, 0, 1);
            $number = substr($blockHouse, 1);

            $user = User::where('email', $email)->first();
            $house = House::where('block', $block)->where('house_number', $number)->first();

            if ($user && $house) {
                $user->houses()->syncWithoutDetaching([$house->id]);
            }
        }
    }
}
