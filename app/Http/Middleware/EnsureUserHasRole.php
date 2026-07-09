<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Membatasi route group berdasarkan role.
     * Dipakai di routes/web.php: ->middleware('role:admin') atau ->middleware('role:warga')
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        abort_unless($request->user() && $request->user()->role === $role, 403);

        return $next($request);
    }
}
