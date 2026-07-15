<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHasPermission
{
    /**
     * Contoh pakai di routes: ->middleware('permission:houses,edit')
     */
    public function handle(Request $request, Closure $next, string $module, string $action = 'view'): Response
    {
        abort_unless($request->user() && $request->user()->canAccess($module, $action), 403);

        return $next($request);
    }
}
