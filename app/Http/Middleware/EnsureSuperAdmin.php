<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureSuperAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->user() || ! $request->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized. Super admin access required.');
        }

        return $next($request);
    }
}
