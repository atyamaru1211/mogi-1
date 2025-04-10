@extends('layout.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/register.css')}}">
@endsection

@section('content')
<div class="register-form">
    <h2 class="register-form__heading">会員登録</h2>
    <div class="register-form__inner">
        <form class="register-form__form">
            <div class="register-form__group">
                <label class="register-form__label" for="name">ユーザー名</label>
                <input class="register-form__input" type="text" name="name" id="name">
                <p class="register-form__error-message">
                    <!---->
                </p>
            </div>
            <div class="register-form__group">
                <label class="register-form__label" for="email">メールアドレス</label>
                <input class="register-form__input" type="email" name="email" id="email">
                <p class="register-form__error-message">
                    <!--エラーメッセージ-->
                </p>
            </div>
            <div class="register-form__group">
                <label class="register-form__label" for="password">パスワード</label>
                <input class="register-form__input" type="password" name="password" id="password">
                <p class="register-form__error-message">
                    <!--エラーメッセージ-->
                </p>
            </div>
            <div class="register-form__group">
                <label class="register-form__label" for="password_confirmation">確認用パスワード</label>
                <input class="register-form__input" type="password" name="password_confirmation" id="password_confirmation">
                <p class="register-form__error-message">
                    <!--エラーメッセージ-->
                </p>
            </div>

            <button class="register-form__button" type="submit">登録する</button>
            <a class="login-link" href="/login">ログインはこちら</a>
        </form>
    </div>
</div>

@endsection