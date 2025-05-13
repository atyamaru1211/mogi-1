@extends('layout.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/login.css')}}">
@endsection

@section('content')
        <div class="login-form__alert">
            @if ($errors->any() && ! $errors->has('email') && ! $errors->has('password'))
                <div class="login-form__alert--danger">
                    @foreach ($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            @endif
        </div>
        <!--<div class="login-form__alert">
            @if ($errors->has('email') && session()->has('auth.password'))
                <div class="login-form__alert--danger">
                    {{ $errors->first('email') }}
                </div>
            @endif
        </div>-->

        <div class="login-form">
            <h2 class="login-form__heading">ログイン</h2>
            <div class="login-form__inner">
                <form class="login-form__form" action="/login" method="post">
                    @csrf
                    <div class="login-form__group">
                        <label class="login-form__label" for="email">メールアドレス</label>
                        <input class="login-form__input" type="email" name="email" id="email" value="{{ old('email') }}">
                        <p class="login-form__error-message">
                            @error('email')
                            {{ $message }}
                            @enderror
                        </p>
                    </div>
                    <div class="login-form__group">
                        <label class="login-form__label" for="password">パスワード</label>
                        <input class="login-form__input" type="password" name="password" id="password">
                        <p class="login-form__error-message">
                            @error('password')
                            {{ $message }}
                            @enderror
                        </p>
                    </div>
                    <button class="login-form__button" type="submit">ログインする</button>
                    <a class="register-link" href="/register">会員登録はこちら</a>
                </form>
            </div>
        </div>
@endsection