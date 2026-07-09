<?php

namespace Database\Seeders;

use App\Models\House;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $houses = House::all();

        // Nominal contoh, cycling per rumah (tidak lagi terikat ke tipe rumah)
        $sampleAmounts = [150000, 200000, 250000];

        foreach ($houses as $index => $house) {
            $amount = $sampleAmounts[$index % count($sampleAmounts)];

            // 3 bulan ke belakang: Mei, Juni, Juli 2026 (disesuaikan dengan waktu seeding)
            foreach (range(2, 0) as $i) {
                $date = Carbon::now()->subMonths($i);

                // Variasi status biar dashboard demo kelihatan realistis:
                // rumah dengan index genap & bulan berjalan -> belum bayar (nunggak/belum jatuh tempo)
                $isCurrentMonth = $i === 0;
                $isUnpaid = $isCurrentMonth && $index % 3 !== 0;

                Payment::create([
                    'house_id' => $house->id,
                    'period_month' => $date->month,
                    'period_year' => $date->year,
                    'amount' => $amount,
                    'status' => $isUnpaid ? 'unpaid' : 'paid',
                    'paid_at' => $isUnpaid ? null : $date->copy()->day(min(5 + $index % 10, 28)),
                    'recorded_by_admin_id' => $admin?->id,
                    'notes' => $isUnpaid ? null : 'Dicatat otomatis oleh seeder demo.',
                ]);
            }
        }
    }
}
