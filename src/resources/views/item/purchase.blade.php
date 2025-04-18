@extends('layout.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item/purchase.css')}}">
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
<form action="">
    <div class="purchase-container">
        <div class="purchase-left">
            <section class="item-section">
                <div class="item-image">
                    <img class="item-image-link" src="" alt="商品画像">
                </div>
                <div class="item-info">
                    <h2 class="item-info__name">商品名</h2>
                    <p class="item-info__price">
                        <span>￥</span>
                        <span>47,000</span>
                    </p>
                </div>
            </section>

            <section class="payment-section">
                <h3 class="payment-section__title">支払い方法</h3>
                <div class="payment__select-inner">
                    <select class="payment__select" name="payment" id="">
                        <option desabled selected>選択してください</option>
                        <!--foreach-->
                    </select>
                </div>
            </section>

            <section class="shipping-section">
                <h3 class="shipping-section__title">配送先</h3>
                <div class="shipping-address">
                    <p class="shipping-address__postal-code">
                        <span>〒</span><span>XXX-YYYY</span>
                    </p>
                    <p class="shipping-address__text">ここには住所と建物が入ります</p>
                    <div class="shipping-address__change">
                        <a class="shipping-address__change-link" href="/purchase/address/:item_id">変更する</a>
                    </div>
                </div>
            </section>
        </div>

        <div class="purchase-right">
            <section class="summary-section">
                <div class="summary-details">
                    <p class="summary-details__price">
                        <span class="price__label">商品代金</span>
                        <span class="price__value">￥ 47,000</span>
                    </p>
                    <div class="divider"></div>
                    <p class="summary-details__payment">
                        <span class="payment__lavel">支払い方法</span>
                        <span class="payment__value">コンビニ支払い</span>
                    </p>
                </div>
            </section>
            <button class="purchase-button" type="submit">購入する</button>
        </div>
    </div>
</form>
@endsection