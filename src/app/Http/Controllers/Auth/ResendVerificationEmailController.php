<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\User;

class ResendVerificationEmailController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if ($user && !$user->hasVerifiedEmail()) {
            $throttleKey = $this->throttleKey($request);
            if (RateLimiter::tooManyAttempts($throttleKey, 6)) {
                return $this->sendLockoutResponse($request, $throttleKey);
            }

            $user->sendEmailVerificationNotification();

            RateLimiter::hit($throttleKey);

            return redirect()->back();
        }

        return redirect()->back();
    }


    protected function sendLockoutResponse(Request $request, string $throttleKey)
    {
        $seconds = RateLimiter::availableIn($throttleKey);

        return response()->json(['message' => __('Too many verification attempts. Please try again in :seconds seconds.', ['seconds' => $seconds])], 429);
    }


    protected function throttleKey(Request $request)
    {
        return 'resend-verification:' . $request->ip();
    }
}
