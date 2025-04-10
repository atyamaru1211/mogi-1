@extends('layout.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/login.css')}}">
@endsection

@section('content')
        <div class="login-form">
            <h2 class="login-form__heading">ログイン</h2>
            <div class="login-form__inner">
                <form class="login-form__form">
                    <div class="login-form__group">
                        <label class="login-form__label" for="email">メールアドレス</label>
                        <input class="login-form__input" type="email" name="email" id="email">
                        <p class="login-form__error-message">
                            <!--エラーメッセージ-->
                        </p>
                    </div>
                    <div class="login-form__group">
                        <label class="login-form__label" for="password">パスワード</label>
                        <input class="login-form__input" type="password" name="password" id="password">
                        <p class="login-form__error-message">
                            <!--エラーメッセージ-->
                        </p>
                    </div>
                    <button class="login-form__button" type="submit">ログインする</button>
                    <a class="register-link" href="/register">会員登録はこちら</a>
                </form>
            </div>
        </div>
@endsection