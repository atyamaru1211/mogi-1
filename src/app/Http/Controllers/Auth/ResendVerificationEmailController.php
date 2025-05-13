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
            if (RateLimiter::tooManyAttempts($request->throttleKey(), 6)) {
                return $this->sendLockoutResponse($request);
            }

            $user->sendEmailVerificationNotification();

            RateLimiter::hit($request->throttleKey());

            return response()->json(['status' => 'verification-link-sent']);
        }

        return response()->json(['status' => 'requires-verification'], 403);
    }


    protected function sendLockoutResponse(Request $request)
    {
        $seconds = RateLimiter::availableIn($request->throttleKey());

        return response()->json(['message' => __('Too many verification attempts. Please try again in :seconds seconds.', ['seconds' => $seconds])], 429);
    }


    protected function throttleKey(Request $request)
    {
        return 'resend-verification:' . $request->ip();
    }
}
