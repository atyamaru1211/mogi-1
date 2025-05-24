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
            <form action="/logout" method="post">
                @csrf
                <button class="header-nav__link" type="submit">ログアウト</button>
            </form>
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
    <div class="purchase-container">
        <div class="purchase-left">
            <section class="item-section">
                <div class="item-image">
                    <img class="item-image-link" src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
                </div>
                <div class="item-info">
                    <h2 class="item-info__name">{{ $item->name }}</h2>
                    <p class="item-info__price">
                        <span>￥</span>
                        <span>{{ number_format($item->price) }}</span>
                    </p>
                </div>
            </section>

            <form action="/purchase/{{ $item->id }}" method="POST" id="payment-selection-form">
                @csrf

                <section class="payment-section">
                    <h3 class="payment-section__title">支払い方法</h3>
                    <div class="payment__select-inner">
                        <select class="payment__select" name="payment_method" id="payment_method"  onchange="this.form.submit()">
                            <option data-display="選択してください" value="">選択してください</option>
                            <option data-display="コンビニ支払い" value="konbini" {{ old('payment_method') == 'konbini' ? 'selected' : '' }}>コンビニ支払い</option>
                            <option data-display="カード支払い" value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>カード支払い</option>
                        </select>
                    </div>
                    <p class="error-message">
                        @error('payment_method')
                        {{ $message }}
                        @enderror
                    </p>
                </section>
            </form>
            <section class="shipping-section">
                <h3 class="shipping-section__title">配送先</h3>
                <div class="shipping-address">
                    <p class="shipping-address__postal-code">
                        <span>〒</span><span>{{ $shippingAddress['postal_code'] ?? $profile->postal_code ?? '' }}</span>
                    </p>
                    <p class="shipping-address__text">
                        <span>
                            {{ $shippingAddress['address'] ?? $profile->address ?? '' }}
                        </span>
                        <span>
                            {{ $shippingAddress['building'] ?? $profile->building ?? '' }}
                        </span>
                    </p>
                    <div class="shipping-address__change">
                        <a class="shipping-address__change-link" href="/purchase/address/{{ $item->id }}">変更する</a>
                    </div>
                    <p class="error-message">
                        @error('address_existence')
                        {{ $message }}
                        @enderror
                    </p>
                </div>
            </section>
        </div>

        <div class="purchase-right">
            <section class="summary-section">
                <div class="summary-details">
                    <p class="summary-details__price">
                        <span class="price__label">商品代金</span>
                        <span class="price__value">￥ {{ number_format($item->price) }}</span>
                    </p>
                    <div class="divider"></div>
                    <p class="summary-details__payment">
                        <span class="payment__lavel">支払い方法</span>
                        <span class="payment__value">
                            @php
                                $selectedMethod = old('payment_method');
                                $displayText = '';
                                if ($selectedMethod === 'konbini') {
                                    $displayText = 'コンビニ支払い';
                                } elseif ($selectedMethod === 'card') {
                                    $displayText = 'カード支払い';
                                }
                            @endphp
                            {{ $displayText }}
                        </span>
                    </p>
                </div>
            </section>
            <form action="/purchase/{{ $item->id }}/checkout" method="POST"> <!---->
                @csrf
                <button class="purchase-button" type="submit">購入する</button>
                <input type="hidden" name="address_existence" value="exists">
                <input type="hidden" name="payment_method" value="{{ old('payment_method') }}"><!---->
                @php
                    $hasShippingAddress = false;
                    if (isset($shippingAddress) && !empty($shippingAddress['address'])) {
                        $hasShippingAddress = true;
                    } elseif (isset($profile) && !empty($profile->address)) {
                        $hasShippingAddress = true;
                    }
                @endphp
                <input type="hidden" name="purchase_price" value="{{ $item->price }}"><!---->
            </form>
        </div>
    </div>

@endsection