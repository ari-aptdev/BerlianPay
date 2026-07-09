<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin BerlianPay',
            'email' => 'admin@berlianpay.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '081200000000',
        ]);

        $residents = [
            ['name' => 'Andi Wijaya', 'email' => 'andi@warga.test', 'phone' => '081234000001'],
            ['name' => 'Siti Rahma', 'email' => 'siti@warga.test', 'phone' => '081234000002'],
            ['name' => 'Budi Santoso', 'email' => 'budi@warga.test', 'phone' => '081234000003'],
            ['name' => 'Dewi Lestari', 'email' => 'dewi@warga.test', 'phone' => '081234000004'],
            ['name' => 'Eko Prasetyo', 'email' => 'eko@warga.test', 'phone' => '081234000005'],
        ];

        foreach ($residents as $resident) {
            User::create([
                ...$resident,
                'password' => Hash::make('password'),
                'role' => 'warga',
            ]);
        }
    }
}
