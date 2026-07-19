<?php

namespace App\Providers;

use App\Models\House;
use App\Models\Payment;
use App\Policies\HousePolicy;
use App\Policies\PaymentPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(House::class, HousePolicy::class);
        Gate::policy(Payment::class, PaymentPolicy::class);

        // Railway (dan hosting lain yang pakai reverse proxy) meneruskan
        // request ke aplikasi lewat HTTP biasa walau publiknya HTTPS.
        // Tanpa ini, Laravel generate form/link pakai http:// sehingga
        // browser menampilkan peringatan "not secure" saat submit form.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Auto-heal symlink storage. Folder public/ di-build ulang dari kode
        // tiap deploy (jadi symlink public/storage ikut hilang), sementara
        // file upload beneran ada di storage/app/public. Tanpa ini, admin
        // harus inget jalanin `php artisan storage:link` manual tiap abis
        // redeploy — sekarang otomatis dicek & dibikin ulang kalau ilang.
        $this->ensureStorageLinkExists();
    }

    protected function ensureStorageLinkExists(): void
    {
        $link = public_path('storage');
        $target = storage_path('app/public');

        if (file_exists($link) || ! is_dir($target)) {
            return;
        }

        try {
            symlink($target, $link);
        } catch (\Throwable $e) {
            // Diam-diam gagal (mis. permission gak izinin symlink) — jangan
            // sampai bikin seluruh aplikasi down cuma gara-gara ini.
        }
    }
}
