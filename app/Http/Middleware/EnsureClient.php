<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureClient
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->user() || ! $request->user()->isClient()) {
            abort(403, 'Unauthorized. Client access required.');
        }

        if (! $request->user()->is_active) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Your account has been deactivated. Contact support.');
        }

        return $next($request);
    }
}
