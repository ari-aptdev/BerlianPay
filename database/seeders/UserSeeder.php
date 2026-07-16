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
            'admin_access_type' => 'full',
            'is_super_admin' => true,
        ]);

        User::create([
            'name' => 'Ketua RT',
            'email' => 'ketuart@berlianpay.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '081200000001',
            'admin_access_type' => 'read_only',
        ]);

        $residents = [
            ['name' => 'Andi Wijaya', 'email' => 'andi@warga.test', 'phone' => '081234000001', 'nik' => '3175011704890007'],
            ['name' => 'Siti Rahma', 'email' => 'siti@warga.test', 'phone' => '081234000002', 'nik' => '3171030905980001'],
            ['name' => 'Budi Santoso', 'email' => 'budi@warga.test', 'phone' => '081234000003', 'nik' => '3216012811920003'],
            ['name' => 'Dewi Lestari', 'email' => 'dewi@warga.test', 'phone' => '081234000004', 'nik' => '3275014209870008'],
            ['name' => 'Eko Prasetyo', 'email' => 'eko@warga.test', 'phone' => '081234000005', 'nik' => '3216012505890009'],
        ];

        foreach ($residents as $resident) {
            User::create([
                ...$resident,
                'username' => User::generateUsername($resident['name'], $resident['nik']),
                'password' => Hash::make('password'),
                'role' => 'warga',
            ]);
        }
    }
}
