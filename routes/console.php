<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jalankan pengecekan reminder setiap hari jam 08:00.
// Pastikan cron server sudah dikonfigurasi untuk memanggil `schedule:run` tiap menit:
// * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
Schedule::command('reminders:check')->dailyAt('08:00');
