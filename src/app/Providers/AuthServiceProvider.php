<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\LoginRateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // ‘App\Models\Model’ => ‘App\Policies\ModelPolicy’,
    ];
    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        
        Fortify::authenticateUsing(function ($request) {
            $validator = Validator::make($request->only('password'), [
                'password' => ['required', 'min:8'],
            ]);

            if ($validator->fails()) {
                throw ValidationException::withMessages([
                    'password' => $validator->errors()->all(),
                ]);
            }

            $username = $request->{Fortify::username()};
            $password = $request->password;

            $user = \App\Models\User::where(Fortify::username(), $username)->first();

            if ($user && \Hash::check($password, $user->password)) {
                return $user;
            }

            return false;
        });
    }
}