<?php

use App\Http\Middleware\EnsureHasPermission;
use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\IdleTimeout;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => EnsureUserHasRole::class,
            'permission' => EnsureHasPermission::class,
        ]);

        // Percayai semua proxy (Railway berjalan di balik load balancer/proxy)
        // supaya Laravel tahu request aslinya HTTPS meski diteruskan via HTTP internal.
        $middleware->trustProxies(at: '*');

        // FIX: kalau user yang SUDAH login buka lagi halaman /login (mis. tab lain
        // di browser yang sama, sesi ke-share), Laravel defaultnya nyoba redirect ke
        // route bernama 'dashboard' yang gak ada di aplikasi ini -> bikin gagal akses.
        // Ini kita override biar redirect ke dashboard sesuai role, bukan error.
        $middleware->redirectUsersTo(function (Request $request) {
            $user = $request->user();

            return $user?->isAdmin() ? route('admin.dashboard') : route('resident.dashboard');
        });

        // Idle timeout: logout otomatis kalau gak ada aktivitas sekian menit (diatur admin).
        $middleware->web(append: [IdleTimeout::class]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
