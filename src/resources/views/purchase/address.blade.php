@extends('layout.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase/address.css')}}">
@endsection

@section('link')
<div class="header-search">
    <form class="search-form" action="/" method="get">
        @csrf
        <input class="search-form__keyword-input" type="text" name="keyword" placeholder="なにをお探しですか？" value="{{ request()->query('keyword') }}">
    </form>
</div>
<nav class="header-nav">
    <ul class="header-nav__list">
        <li class="header-nav__item">
            <a class="header-nav__link" href="/logout">ログアウト</a>
        </li>
        <li class="header-nav__item">
            <a class="header-nav__link" href="/mypage">マイページ</a>
        </li>
        <li class="header-nav__item">
            <a class="header-nav__button" href="/sell">出品</a>
        </li>
    </ul>
</nav>
@endsection

@section('content')
<div class="address-edit__container">
    <h2 class="address-edit__title">住所の変更</h2>
    <form class="address-edit__form">
        <div class="form-group">
            <label class="form-label" for="postal_code">郵便番号</label>
            <input class="form-input" type="text" name="postal_code" id="postal_code">
        </div>
        <div class="form-group">
            <label class="form-label" for="address">住所</label>
            <input class="form-input" type="text" name="address" id="address">
        </div>
        <div class="form-group">
            <label class="form-label" for="building">建物名</label>
            <input class="form-input" type="text" name="building" id="building">
        </div>
        <button class="address-edit__button" type="submit">更新する</button>
    </form>
</div>
@endsection