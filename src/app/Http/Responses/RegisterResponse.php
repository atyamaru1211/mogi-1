<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Session;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request)
    {
        Session::put('just_registered', true);
        return redirect()->intended('/email/verify/notice');
    }
}
