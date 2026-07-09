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
    }
}
