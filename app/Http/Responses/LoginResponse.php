<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = Auth::user();
        if ($user->role === 'contractor') {
            return redirect()->intended('/tasks');
        }
        return redirect()->intended(config('fortify.home', '/dashboard'));
    }
}
