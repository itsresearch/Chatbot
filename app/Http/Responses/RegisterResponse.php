<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request)
    {
        if ($request->wantsJson()) {
            return new JsonResponse('', 201);
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
