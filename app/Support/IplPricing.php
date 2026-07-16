<?php

namespace App\Support;

use App\Models\Setting;

class IplPricing
{
    /**
     * Ambil nominal tiap komponen iuran (bisa diedit admin di halaman Tarif IPL).
     */
    public static function components(): array
    {
        return [
            'kas' => (int) Setting::get('ipl_kas', 10000),
            'kebersihan' => (int) Setting::get('ipl_kebersihan', 30000),
            'keamanan' => (int) Setting::get('ipl_keamanan', 40000),
            'rukem_tambahan' => (int) Setting::get('ipl_rukem_tambahan', 5000),
        ];
    }

    public static function registrationFee(): int
    {
        return (int) Setting::get('rukem_registration_fee', 10000);
    }

    /**
     * Rincian iuran (nama => nominal) sesuai status Rukem/Non-Rukem.
     * Rukem dapat tambahan baris "Iuran Rukem".
     */
    public static function breakdownFor(string $iplStatus): array
    {
        $c = self::components();

        $breakdown = [
            'Iuran Kas' => $c['kas'],
            'Iuran Kebersihan' => $c['kebersihan'],
            'Iuran Keamanan' => $c['keamanan'],
        ];

        if ($iplStatus === 'rukem') {
            $breakdown['Iuran Rukem'] = $c['rukem_tambahan'];
        }

        return $breakdown;
    }

    public static function totalFor(string $iplStatus): int
    {
        return array_sum(self::breakdownFor($iplStatus));
    }
}
