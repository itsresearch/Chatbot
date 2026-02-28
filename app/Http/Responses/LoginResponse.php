<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        if ($request->wantsJson()) {
            return response()->json(['two_factor' => false]);
        }

        $user = $request->user();

        if ($user && $user->isSuperAdmin()) {
            return redirect()->route('superadmin.dashboard');
        }

        if ($user && $user->isClient()) {
            return redirect()->route('client.dashboard');
        }

        return redirect('/dashboard');
    }
}
