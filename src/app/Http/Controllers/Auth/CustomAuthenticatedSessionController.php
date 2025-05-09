<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\FailedPasswordResetLink;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;

class CustomAuthenticatedSessionController extends Controller
{
    // ログイン処理
    public function store(LoginRequest $request)
    {
        $request->authenticate();
    
        $this->ensureIsNotRateLimited();
    
        if (! Auth::attempt($request->only(Fortify::username(), 'password'), $request->boolean('remember'))) {
            Fortify::logout();
            throw ValidationException::withMessages([
                Fortify::username() => ['ログイン情報が登録されていません。'], 
            ]);
        }
    
        $user = Auth::user();
    
        if (! $user->hasVerifiedEmail()) {
            Auth::logout();
            return redirect()->route('verification.notice')->with('message', 'メールアドレスの認証が必要です。受信した認証メール内のリンクをクリックしてください。');
        }
    
        $request->session()->regenerate();
    
        return redirect('/');//->intended(RouteServiceProvider::HOME);
    }

    // ログアウト処理
    public function destroy(Request $request)
    {
        Auth::guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    //レートリミット
    protected function ensureIsNotRateLimited()
    {
        if (! Features::enabled(Features::limiter())) {
            return;
        }

        $seconds = $this->limiter()->availableIn(
            $this->throttleKey()
        );

        if ($seconds > 0) {
            throw ValidationException::withMessages([
                Fortify::username() => [__('Too many login attempts. Please try again in :seconds seconds.', [
                    'seconds' => $seconds,
                ])],
            ])->status(429);
        }

        $this->limiter()->hit($this->throttleKey());
    }

    // レートリミットのキー生成
    protected function throttleKey()
    {
        return strtolower($this->username()).'|'.$this->ip();
    }

    // ログインに使用するユーザー名取得
    public function username()
    {
        return Fortify::username();
    }

    // 
    protected function rules()
    {
        return [
            Fortify::username() => 'required|string',
            'password' => 'required|string',
        ];
    }
}