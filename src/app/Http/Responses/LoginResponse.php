<?php

namespace App\Http\Responses;

use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;

class LoginResponse implements Responsable
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        if (Session::pull('just_registered', false)) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        return redirect()->intended('/');
    }
}