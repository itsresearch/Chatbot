<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectByRole
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->isSuperAdmin()) {
            return redirect()->route('superadmin.dashboard');
        }

        if ($user && $user->isClient()) {
            return redirect()->route('client.dashboard');
        }

        return $next($request);
    }
}
