<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Wrapper sederhana untuk kirim pesan WhatsApp lewat API pihak ketiga.
 *
 * Default: Fonnte (https://fonnte.com). Ganti method send() ini kalau
 * client pakai provider lain (Wablas/Twilio) — cukup ubah di satu tempat.
 *
 * WAJIB isi WA_API_URL & WA_API_KEY di .env sebelum fitur WA reminder aktif.
 * Selama API key belum diisi, service ini hanya akan mencatat log tanpa
 * benar-benar mengirim (biar aman dipakai sebelum provider final dipilih).
 */
class WhatsAppService
{
    public function send(string $phone, string $message): bool
    {
        $apiKey = config('services.whatsapp.api_key');
        $apiUrl = config('services.whatsapp.api_url');

        if (empty($apiKey)) {
            Log::info('[WhatsAppService] API key kosong, reminder WA di-skip (dry-run).', [
                'phone' => $phone,
                'message' => $message,
            ]);

            return false;
        }

        $response = Http::withHeaders(['Authorization' => $apiKey])
            ->asForm()
            ->post($apiUrl, [
                'target' => $phone,
                'message' => $message,
            ]);

        if ($response->failed()) {
            Log::warning('[WhatsAppService] Gagal kirim WA.', [
                'phone' => $phone,
                'response' => $response->body(),
            ]);
        }

        return $response->successful();
    }
}
