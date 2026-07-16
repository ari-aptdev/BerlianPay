<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IdleTimeout
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $timeoutMinutes = (int) Setting::get('session_timeout_minutes', 30);
            $lastActivity = $request->session()->get('last_activity_at');

            if ($lastActivity && now()->diffInMinutes($lastActivity) >= $timeoutMinutes) {
                $wasAdmin = $request->user()->isAdmin();

                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('idle_timeout', 'Sesi kamu berakhir karena tidak ada aktivitas. Silakan login lagi.');
            }

            $request->session()->put('last_activity_at', now());
        }

        return $next($request);
    }
}
